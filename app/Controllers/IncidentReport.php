<?php

namespace App\Controllers;

use App\Models\IncidentReportModel;


class IncidentReport extends BaseController
{
    private IncidentReportModel $incidentReportModel;

    public function __construct()
    {
        $this->incidentReportModel = new IncidentReportModel();
    }

    /**
     * Update a single incident by N (number)
     * Expects JSON body with fields to update
     */
    public function updateIncident($n)
    {
        if (!session()->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON([
                'message' => 'Unauthorized.',
            ]);
        }

        $incident = $this->incidentReportModel->where('n', $n)->first();
        if (!$incident) {
            return $this->response->setStatusCode(404)->setJSON([
                'message' => 'Incident not found.',
            ]);
        }

        $data = $this->request->getJSON(true);
        if (!is_array($data)) {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => 'Invalid data.'
            ]);
        }

        // normalize gender value before further processing so the same format
        // used by createIncident is preserved during updates.
        if (isset($data['gender'])) {
            $data['gender'] = $this->normalizeGender($data['gender']);
        }

        // Reject invalid year values early (prevent bypassing client-side UI)
        if (isset($data['year_of_incident'])) {
            $yearValue = $this->toInt($data['year_of_incident']);
            $currentYear = (int) date('Y');
            if ($yearValue === null || $yearValue < 2000 || $yearValue > $currentYear) {
                return $this->response->setStatusCode(400)->setJSON([
                    'message' => 'Invalid year_of_incident.'
                ]);
            }
        }

        // Validate age (if provided) — must be integer 0..120
        if (isset($data['age'])) {
            $ageValue = $this->toInt($data['age']);
            if (!is_null($data['age']) && ($ageValue === null || $ageValue < 0 || $ageValue > 120)) {
                return $this->response->setStatusCode(400)->setJSON([
                    'message' => 'Invalid age.'
                ]);
            }
        }

        // Remove fields that should not be updated
        unset($data['n']);
        unset($data['id']);

        // Ensure any plaintext victim name is encrypted/hash-backed before saving
        $data = $this->incidentReportModel->prepareForInsert($data);
        $this->incidentReportModel->update($incident['id'], $data);

        return $this->response->setJSON([
            'message' => 'Incident updated successfully.'
        ]);
    }

    /**
     * Create a single incident (used by UI Add Incident -> Save)
     * Accepts JSON body with DB field names (month_of_incident, year_of_incident, province, municipality, name_of_victim, ...)
     */
    public function createIncident()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON([
                'message' => 'Unauthorized.'
            ]);
        }

        $roleName = strtoupper(trim((string) session()->get('role_name')));
        $isAdmin = (bool) session()->get('is_admin');
        // Allow LGU users and administrators to create incidents (UI already shows Add Incident to admins)
        if ($roleName !== 'LGU' && !$isAdmin) {
            return $this->response->setStatusCode(403)->setJSON([
                'message' => 'Only LGU users or administrators can create incidents.'
            ]);
        }

        $data = $this->request->getJSON(true);
        if (!is_array($data)) {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => 'Invalid payload.'
            ]);
        }

        // Basic required fields
        $required = ['month_of_incident', 'year_of_incident', 'province', 'municipality'];
        foreach ($required as $f) {
            if (!isset($data[$f]) || $data[$f] === '') {
                return $this->response->setStatusCode(400)->setJSON([
                    'message' => 'Missing required fields.'
                ]);
            }
        }

        $monthValue = $this->toInt($data['month_of_incident']);
        if ($monthValue === null || $monthValue < 1 || $monthValue > 12) {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => 'Invalid month_of_incident (must be 1-12).'
            ]);
        }

        // Validate year_of_incident to prevent arbitrary values (server-side enforcement)
        $yearValue = $this->toInt($data['year_of_incident'] ?? null);
        $currentYear = (int) date('Y');
        if ($yearValue === null || $yearValue < 2000 || $yearValue > $currentYear) {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => 'Invalid year_of_incident.'
            ]);
        }

        // Validate age if present (must be integer 0..120)
        if (isset($data['age']) && $data['age'] !== '') {
            $ageValue = $this->toInt($data['age']);
            if ($ageValue === null || $ageValue < 0 || $ageValue > 120) {
                return $this->response->setStatusCode(400)->setJSON([
                    'message' => 'Invalid age.'
                ]);
            }
        }

        // Normalize incoming payload similar to import/mapRow
        $mapped = [];
        // assign next available incident number (n) — required by DB
        $mapped['n'] = $this->getNextIncidentNumber();
        $mapped['month_of_incident'] = $this->toString($data['month_of_incident'] ?? '');
        $mapped['year_of_incident'] = $this->toInt($data['year_of_incident'] ?? null);
        $mapped['province'] = $this->toString($data['province'] ?? '');
        $mapped['municipality'] = $this->toString($data['municipality'] ?? '');
        $mapped['name_of_victim'] = $this->toString($data['name_of_victim'] ?? '');
        $mapped['location_category'] = $this->toString($data['location_category'] ?? '');
        $mapped['age'] = $this->toInt($data['age'] ?? null);
        $mapped['gender'] = $this->normalizeGender($data['gender'] ?? '');
        $mapped['occasion'] = $this->toString($data['occasion'] ?? '');
        $mapped['factors'] = $this->toString($data['factors'] ?? '');
        $mapped['residence'] = $this->toString($data['residence'] ?? '');
        $mapped['occupation'] = $this->toString($data['occupation'] ?? '');
        $mapped['remarks'] = $this->toString($data['remarks'] ?? '');

        $mapped['row_hash'] = $this->hashRow($mapped);

        // Avoid duplicates: if row with same hash exists, return it instead of inserting
        $existing = $this->incidentReportModel->where('row_hash', $mapped['row_hash'])->first();
        if ($existing) {
            $existing = $this->incidentReportModel->decryptRow($existing);
            return $this->response->setJSON([
                'message' => 'Incident already exists.',
                'incident' => $existing,
            ]);
        }

        // Ensure any plaintext victim name is encrypted/hash-backed before saving
        $mapped = $this->incidentReportModel->prepareForInsert($mapped);

        // Insert and return created row
        $insertId = $this->incidentReportModel->insert($mapped);
        if ($insertId === false) {
            $errors = $this->incidentReportModel->errors();
            return $this->response->setStatusCode(500)->setJSON([
                'message' => 'Insert failed.',
                'errors' => $errors,
            ]);
        }

        $created = $this->incidentReportModel->find($insertId);
        if ($created) {
            $created = $this->incidentReportModel->decryptRow($created);

            // If client provided an attachment session, move those temp files into the new incident
            $sessionToken = isset($data['attachment_session']) ? trim((string) $data['attachment_session']) : '';
            if ($sessionToken !== '') {
                $tmpDir = WRITEPATH . 'uploads/incident_reports/tmp/' . $sessionToken;
                if (is_dir($tmpDir)) {
                    $files = array_values(array_filter(scandir($tmpDir), function ($f) use ($tmpDir) {
                        return is_file($tmpDir . DIRECTORY_SEPARATOR . $f) && substr($f, -5) !== '.json';
                    }));

                    $attachmentModel = new \App\Models\IncidentReportAttachmentModel();
                    $saved = 0;

                    $destDir = WRITEPATH . 'uploads/incident_reports/' . $created['n'];
                    if (!is_dir($destDir)) mkdir($destDir, 0775, true);

                    foreach ($files as $fileName) {
                        $metaPath = $tmpDir . DIRECTORY_SEPARATOR . $fileName . '.json';
                        $meta = [];
                        if (is_file($metaPath)) {
                            $content = file_get_contents($metaPath);
                            $meta = json_decode($content, true) ?: [];
                        }

                        $origName = $meta['original_name'] ?? $fileName;
                        $mimeType = $meta['mime_type'] ?? mime_content_type($tmpDir . DIRECTORY_SEPARATOR . $fileName);
                        $size = $meta['size_bytes'] ?? filesize($tmpDir . DIRECTORY_SEPARATOR . $fileName);

                        $destName = $fileName;
                        // move
                        rename($tmpDir . DIRECTORY_SEPARATOR . $fileName, $destDir . DIRECTORY_SEPARATOR . $destName);

                        $storedPath = 'incident_reports/' . $created['n'] . '/' . $destName;

                        $fileKind = str_starts_with($mimeType, 'image/') ? 'photo' : 'document';

                        $attachmentModel->insert([
                            'incident_n' => $created['n'],
                            'file_kind' => $fileKind,
                            'original_name' => $origName,
                            'stored_name' => $destName,
                            'stored_path' => $storedPath,
                            'mime_type' => $mimeType,
                            'size_bytes' => (int) $size,
                            'uploaded_by' => (int) session()->get('user_id'),
                        ]);

                        // remove meta file if exists
                        if (is_file($metaPath)) @unlink($metaPath);
                        $saved++;
                    }

                    // remove tmp dir if empty
                    @rmdir($tmpDir);
                }

                // refresh attachments count
                $attachmentModel = new \App\Models\IncidentReportAttachmentModel();
                $count = $attachmentModel->where('incident_n', $created['n'])->countAllResults();
                $created['attachments_count'] = $count;
            } else {
                // Count attachments (should be 0 for newly created)
                $attachmentModel = new \App\Models\IncidentReportAttachmentModel();
                $count = $attachmentModel->where('incident_n', $created['n'])->countAllResults();
                $created['attachments_count'] = $count;
            }
        }

        return $this->response->setJSON([
            'message' => 'Incident created.',
            'incident' => $created,
        ]);
    }

    /**
     * Generate incident report (excluding victim names)
     * Returns JSON or downloadable file (CSV)
     */
        /**
         * Generate incident report (excluding victim names)
         * Returns JSON or downloadable file (CSV)
         */
        public function generateReport()
        {
            if (!session()->get('logged_in')) {
                return $this->response->setStatusCode(401)->setJSON([
                    'message' => 'Unauthorized.',
                ]);
            }

            // Fetch all incidents
            $incidents = $this->incidentReportModel->findAll();

            // Columns to include (excluding victim name)
            $columns = [
                'n',
                'month_of_incident',
                'year_of_incident',
                'province',
                'municipality',
                'location_category',
                'age',
                'gender',
                'occasion',
                'factors',
                'residence',
                'occupation',
                'remarks',
            ];

            // Prepare data
            $data = [];
            foreach ($incidents as $incident) {
                $row = [];
                foreach ($columns as $col) {
                    $row[$col] = $incident[$col] ?? '';
                }
                $data[] = $row;
            }

            // Optionally: return as CSV file
            if ($this->request->getGet('format') === 'csv') {
                $filename = 'incident_report_' . date('Ymd_His') . '.csv';
                $csv = $this->arrayToCsv($data, $columns);
                return $this->response
                    ->setHeader('Content-Type', 'text/csv')
                    ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
                    ->setBody($csv);
            }

            // Default: return as JSON
            return $this->response->setJSON([
                'columns' => $columns,
                'data' => $data,
            ]);
        }

        /**
         * Helper: Convert array to CSV
         */
        private function arrayToCsv(array $data, array $columns): string
        {
            $output = fopen('php://temp', 'r+');
            // Write header
            fputcsv($output, $columns);
            // Write rows
            foreach ($data as $row) {
                $line = [];
                foreach ($columns as $col) {
                    $line[] = $row[$col] ?? '';
                }
                fputcsv($output, $line);
            }
            rewind($output);
            $csv = stream_get_contents($output);
            fclose($output);
            return $csv;
        }

    public function import()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON([
                'message' => 'Unauthorized.',
            ]);
        }

        $roleName = strtoupper(trim((string) session()->get('role_name')));
        if ($roleName !== 'LGU') {
            return $this->response->setStatusCode(403)->setJSON([
                'message' => 'Only LGU users can upload incident report files.',
            ]);
        }

        $payload = $this->request->getJSON(true);
        $rows = $payload['rows'] ?? null;
        if (!is_array($rows)) {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => 'Invalid payload. Expected rows array.',
            ]);
        }

        $required = ['month_of_incident', 'year_of_incident', 'province', 'municipality'];

        $inserted = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];
        $nextN = $this->getNextIncidentNumber();

        foreach ($rows as $index => $row) {
            if (!is_array($row)) {
                $skipped++;
                continue;
            }

            // Defensive merge: if the uploaded row contains separate name parts
            // (Last/First/Middle or Surname/Given), combine them into a single
            // `Name of Victim` field so downstream mapping always sees the full name.
            $nameParts = [];
            $partKeys = ['Last Name', 'First Name', 'Middle Name', 'Surname', 'Given Name'];
            foreach ($partKeys as $pk) {
                if (isset($row[$pk]) && trim((string) $row[$pk]) !== '') {
                    $nameParts[] = trim((string) $row[$pk]);
                }
            }
            if (!empty($nameParts)) {
                $row['Name of Victim'] = implode(' ', $nameParts);
            }

            $mapped = $this->mapRow($row, $index);
            // Always disregard 'n' from Excel and assign incrementally
            $mapped['n'] = $nextN;
            $nextN++;
            $mapped['row_hash'] = $this->hashRow($mapped);

            $monthValue = $this->toInt($mapped['month_of_incident'] ?? null);
            if ($monthValue === null || $monthValue < 1 || $monthValue > 12) {
                $skipped++;
                $errors[] = [
                    'row' => $index + 1,
                    'invalid' => ['month_of_incident'],
                ];
                continue;
            }
            $mapped['month_of_incident'] = $monthValue;

            $missing = $this->getMissingFields($mapped, $required);
            if (!empty($missing)) {
                $skipped++;
                $errors[] = [
                    'row' => $index + 1,
                    'missing' => $missing,
                ];
                continue;
            }

            $existingRow = $this->incidentReportModel
                ->where('row_hash', $mapped['row_hash'])
                ->first();

            if ($existingRow) {
                // Check if all columns match exactly; if so, we can skip the row
                $allMatch = true;
                foreach ($mapped as $col => $val) {
                    if (isset($existingRow[$col]) && $existingRow[$col] !== $val) {
                        $allMatch = false;
                        break;
                    }
                }
                if ($allMatch) {
                    // Skip duplicate
                    $skipped++;
                    continue;
                }

                // Not an exact duplicate – update the existing row
                $mapped = $this->incidentReportModel->prepareForInsert($mapped);
                $this->incidentReportModel->update($existingRow['id'], $mapped);
                $updated++;
                continue;
            }

            // New row, prepare and insert
            $mapped = $this->incidentReportModel->prepareForInsert($mapped);
            $this->incidentReportModel->insert($mapped);
            $inserted++;
        }

        return $this->response->setJSON([
            'message' => 'Import completed.',
            'inserted' => $inserted,
            'updated' => $updated,
            'skipped' => $skipped,
            'errors' => $errors,
        ]);
    }

    public function uploadAttachment()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON([
                'message' => 'Unauthorized.',
            ]);
        }

        $roleName = strtoupper(trim((string) session()->get('role_name')));
        if ($roleName !== 'LGU') {
            return $this->response->setStatusCode(403)->setJSON([
                'message' => 'Only LGU users can upload incident attachments.',
            ]);
        }

        $incidentN = (int) $this->request->getPost('incident_n');
        if ($incidentN <= 0) {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => 'Missing incident number.',
            ]);
        }

        $incident = $this->incidentReportModel->where('n', $incidentN)->first();
        if (!$incident) {
            return $this->response->setStatusCode(404)->setJSON([
                'message' => 'Incident not found.',
            ]);
        }

        $files = $this->request->getFileMultiple('attachments');
        if (!$files) {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => 'Please upload at least one file.',
            ]);
        }

        $allowedTypes = [
            'image/jpeg',
            'image/png',
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ];

        $uploadDir = WRITEPATH . 'uploads/incident_reports/' . $incidentN;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        $attachmentModel = new \App\Models\IncidentReportAttachmentModel();
        $saved = 0;

        foreach ($files as $file) {
            if (!$file || !$file->isValid() || $file->hasMoved()) {
                continue;
            }

            if (!in_array($file->getClientMimeType(), $allowedTypes, true)) {
                continue;
            }

            $storedName = $file->getRandomName();
            $file->move($uploadDir, $storedName);

            $mimeType = $file->getClientMimeType();
            $fileKind = str_starts_with($mimeType, 'image/') ? 'photo' : 'document';

            $attachmentId = $attachmentModel->insert([
                'incident_n' => $incidentN,
                'file_kind' => $fileKind,
                'original_name' => $file->getClientName(),
                'stored_name' => $storedName,
                'stored_path' => 'incident_reports/' . $incidentN . '/' . $storedName,
                'preview_path' => null,
                'preview_mime' => null,
                'mime_type' => $mimeType,
                'size_bytes' => $file->getSize(),
                'uploaded_by' => (int) session()->get('user_id'),
            ], true);

            if ($attachmentId && $this->isOfficeDocument(['mime_type' => $mimeType])) {
                $this->queueAttachmentPreviewConversion((int) $attachmentId);
            }

            $saved++;
        }

        if ($saved === 0) {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => 'No valid files were uploaded.',
            ]);
        }

        $this->incidentReportModel->update($incident['id'], [
            'review_status' => 'pending',
            'reviewed_by' => null,
            'reviewed_at' => null,
        ]);

        $count = $attachmentModel->where('incident_n', $incidentN)->countAllResults();

        return $this->response->setJSON([
            'message' => 'Attachments uploaded successfully.',
            'attachments_count' => $count,
        ]);
    }

    /**
     * Upload attachments to a temporary session (used before an incident is created)
     * POST fields: attachments[], session_token (optional)
     * Returns: { session_token, files: [{ original_name, stored_name, mime_type, size_bytes }] }
     */
    public function uploadTempAttachment()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON(['message' => 'Unauthorized.']);
        }

        $roleName = strtoupper(trim((string) session()->get('role_name')));
        if ($roleName !== 'LGU') {
            return $this->response->setStatusCode(403)->setJSON(['message' => 'Only LGU users can upload incident attachments.']);
        }

        $sessionToken = $this->request->getPost('session_token') ?: bin2hex(random_bytes(12));
        $files = $this->request->getFileMultiple('attachments');
        if (!$files) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Please upload at least one file.']);
        }

        $allowedTypes = [
            'image/jpeg',
            'image/png',
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ];

        $tmpDir = WRITEPATH . 'uploads/incident_reports/tmp/' . $sessionToken;
        if (!is_dir($tmpDir)) mkdir($tmpDir, 0775, true);

        $uploaded = [];
        foreach ($files as $file) {
            if (!$file || !$file->isValid() || $file->hasMoved()) {
                continue;
            }
            $mime = $file->getClientMimeType();
            if (!in_array($mime, $allowedTypes, true)) {
                continue;
            }

            $stored = $file->getRandomName();
            $file->move($tmpDir, $stored);

            $meta = [
                'original_name' => $file->getClientName(),
                'mime_type' => $mime,
                'size_bytes' => $file->getSize(),
                'uploaded_by' => (int) session()->get('user_id'),
                'created_at' => date('Y-m-d H:i:s'),
            ];
            @file_put_contents($tmpDir . DIRECTORY_SEPARATOR . $stored . '.json', json_encode($meta));

            $uploaded[] = [
                'original_name' => $meta['original_name'],
                'stored_name' => $stored,
                'mime_type' => $meta['mime_type'],
                'size_bytes' => $meta['size_bytes'],
            ];
        }

        if (empty($uploaded)) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'No valid files were uploaded.']);
        }

        return $this->response->setJSON(['session_token' => $sessionToken, 'files' => $uploaded]);
    }

    /**
     * Remove a temporary attachment previously uploaded with a session token.
     * POST: session_token, stored_name
     */
    public function removeTempAttachment()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON(['message' => 'Unauthorized.']);
        }

        $roleName = strtoupper(trim((string) session()->get('role_name')));
        if ($roleName !== 'LGU') {
            return $this->response->setStatusCode(403)->setJSON(['message' => 'Only LGU users can remove attachments.']);
        }

        $sessionToken = $this->request->getPost('session_token') ?: '';
        $storedName = $this->request->getPost('stored_name') ?: '';
        if ($sessionToken === '' || $storedName === '') {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Missing parameters.']);
        }

        $path = WRITEPATH . 'uploads/incident_reports/tmp/' . $sessionToken . DIRECTORY_SEPARATOR . $storedName;
        $metaPath = $path . '.json';
        $deleted = false;
        if (is_file($path)) { @unlink($path); $deleted = true; }
        if (is_file($metaPath)) { @unlink($metaPath); }

        return $this->response->setJSON(['deleted' => $deleted]);
    }

    public function approve(int $incidentN)
    {
        return $this->updateReviewStatus($incidentN, 'approved');
    }

    public function reject(int $incidentN)
    {
        return $this->updateReviewStatus($incidentN, 'rejected');
    }

    private function updateReviewStatus(int $incidentN, string $status)
    {
        if (!session()->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON([
                'message' => 'Unauthorized.',
            ]);
        }

        $roleName = strtoupper(trim((string) session()->get('role_name')));
        $isAdmin = (bool) session()->get('is_admin');
        if ($roleName !== 'PROVINCE' && !$isAdmin) {
            return $this->response->setStatusCode(403)->setJSON([
                'message' => 'You do not have permission to review incidents.',
            ]);
        }

        $incident = $this->incidentReportModel->where('n', $incidentN)->first();
        if (!$incident) {
            return $this->response->setStatusCode(404)->setJSON([
                'message' => 'Incident not found.',
            ]);
        }

        $this->incidentReportModel->update($incident['id'], [
            'review_status' => $status,
            'reviewed_by' => (int) session()->get('user_id'),
            'reviewed_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON([
            'message' => 'Incident updated.',
            'status' => $status,
        ]);
    }

    public function listAttachments(int $incidentN)
    {
        if (!session()->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON([
                'message' => 'Unauthorized.',
            ]);
        }

        $incident = $this->incidentReportModel->where('n', $incidentN)->first();
        if (!$incident) {
            return $this->response->setStatusCode(404)->setJSON([
                'message' => 'Incident not found.',
            ]);
        }

        $attachmentModel = new \App\Models\IncidentReportAttachmentModel();
        $attachments = $attachmentModel
            ->where('incident_n', $incidentN)
            ->orderBy('created_at', 'desc')
            ->findAll();

        // Provide per-type counts so the UI can display "X photos, Y documents"
        $counts = ['photo' => 0, 'document' => 0];
        foreach ($attachments as $a) {
            $kind = $a['file_kind'] ?? (str_starts_with($a['mime_type'] ?? '', 'image/') ? 'photo' : 'document');
            if ($kind === 'photo') {
                $counts['photo']++;
            } else {
                $counts['document']++;
            }
        }

        return $this->response->setJSON([
            'attachments' => $attachments,
            'counts' => $counts,
        ]);
    }

    public function viewAttachment(int $attachmentId)
    {
        if (!session()->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON([
                'message' => 'Unauthorized.',
            ]);
        }

        $attachmentModel = new \App\Models\IncidentReportAttachmentModel();
        $attachment = $attachmentModel->find($attachmentId);
        if (!$attachment) {
            return $this->response->setStatusCode(404)->setJSON([
                'message' => 'Attachment not found.',
            ]);
        }

        $previewPath = $attachment['preview_path'] ?? null;
        $previewMime = $attachment['preview_mime'] ?? null;
        $filePath = null;
        $mimeType = null;

        if ($previewPath) {
            $previewFullPath = WRITEPATH . 'uploads/' . $previewPath;
            if (is_file($previewFullPath)) {
                $filePath = $previewFullPath;
                $mimeType = $previewMime ?: 'application/pdf';
            }
        }

        $originalPath = WRITEPATH . 'uploads/' . $attachment['stored_path'];
        if (!$filePath && is_file($originalPath)) {
            $filePath = $originalPath;
            $mimeType = $attachment['mime_type'] ?: 'application/octet-stream';
        }

        if (!$filePath && is_file($originalPath) && $this->isOfficeDocument($attachment)) {
            $this->queueAttachmentPreviewConversion($attachmentId);

            if ($this->isAttachmentPreviewQueued($attachmentId)) {
                return $this->response
                    ->setHeader('Content-Type', 'text/html; charset=UTF-8')
                    ->setBody('<div style="padding:16px;font-family:Arial,sans-serif;">Converting preview... please wait.<script>setTimeout(function(){location.reload();}, 3000);</script></div>');
            }
        }

        if (!$filePath) {
            return $this->response->setStatusCode(404)->setJSON([
                'message' => 'File not found on server.',
            ]);
        }

        // STREAM the file directly with Content-Length to avoid browser decoding issues
        $filesize = filesize($filePath);
        $stream = fopen($filePath, 'rb');
        if ($stream === false) {
            return $this->response->setStatusCode(500)->setJSON(['message' => 'Unable to read file.']);
        }

        $body = stream_get_contents($stream);
        fclose($stream);

        return $this->response
            ->setHeader('Content-Type', $mimeType)
            ->setHeader('Content-Length', (string) $filesize)
            ->setHeader('Content-Disposition', 'inline; filename="' . addslashes($attachment['original_name']) . '"')
            ->setBody($body);
    }

    /**
     * Return attachment preview data (base64) for robust in-page previews.
     * Client JS uses this to render images reliably inside the modal.
     */
    public function previewAttachment(int $attachmentId)
    {
        if (!session()->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON(['message' => 'Unauthorized.']);
        }

        $attachmentModel = new \App\Models\IncidentReportAttachmentModel();
        $attachment = $attachmentModel->find($attachmentId);
        if (!$attachment) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'Attachment not found.']);
        }

        // Prefer preview_path when available (converted PDF for office docs)
        $previewPath = $attachment['preview_path'] ?? null;
        if ($previewPath) {
            $filePath = WRITEPATH . 'uploads/' . $previewPath;
            if (!is_file($filePath)) {
                $filePath = WRITEPATH . 'uploads/' . ($attachment['stored_path'] ?? '');
            }
        } else {
            $filePath = WRITEPATH . 'uploads/' . ($attachment['stored_path'] ?? '');
        }

        if (!is_file($filePath)) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'File not found on server.']);
        }

        // If the original is an office document and no preview exists yet, queue conversion
        if ($this->isOfficeDocument($attachment) && empty($attachment['preview_path'])) {
            $this->queueAttachmentPreviewConversion($attachmentId);
            return $this->response->setStatusCode(202)->setJSON(['converting' => true, 'message' => 'Preview conversion queued.']);
        }

        $mimeType = $attachment['preview_mime'] ?? $attachment['mime_type'] ?? mime_content_type($filePath) ?? 'application/octet-stream';

        // Read and base64-encode. Small/medium images/PDFs are acceptable to inline.
        $contents = file_get_contents($filePath);
        if ($contents === false) {
            return $this->response->setStatusCode(500)->setJSON(['message' => 'Failed to read file.']);
        }

        $data = base64_encode($contents);

        return $this->response->setJSON([
            'mime_type' => $mimeType,
            'data' => $data,
        ]);
    }

    public function downloadAttachment(int $attachmentId)
    {
        if (!session()->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON([
                'message' => 'Unauthorized.',
            ]);
        }

        $attachmentModel = new \App\Models\IncidentReportAttachmentModel();
        $attachment = $attachmentModel->find($attachmentId);
        if (!$attachment) {
            return $this->response->setStatusCode(404)->setJSON([
                'message' => 'Attachment not found.',
            ]);
        }

        $fullPath = WRITEPATH . 'uploads/' . $attachment['stored_path'];
        if (!is_file($fullPath)) {
            return $this->response->setStatusCode(404)->setJSON([
                'message' => 'File not found on server.',
            ]);
        }

        return $this->response->download($fullPath, null)->setFileName($attachment['original_name']);
    }

    private function isOfficeDocument(array $document): bool
    {
        return in_array($document['mime_type'] ?? '', [
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ], true);
    }

    private function isAttachmentPreviewQueued(int $attachmentId): bool
    {
        return is_file($this->getAttachmentPreviewQueueLockPath($attachmentId));
    }

    private function getAttachmentPreviewQueueLockPath(int $attachmentId): string
    {
        return WRITEPATH . 'uploads/incident_reports/previews/.queue-' . $attachmentId;
    }

    private function queueAttachmentPreviewConversion(int $attachmentId): void
    {
        if (!function_exists('exec')) {
            return;
        }

        $previewDir = WRITEPATH . 'uploads/incident_reports/previews';
        if (!is_dir($previewDir)) {
            mkdir($previewDir, 0775, true);
        }

        $lockPath = $this->getAttachmentPreviewQueueLockPath($attachmentId);
        if (is_file($lockPath)) {
            return;
        }

        @file_put_contents($lockPath, date('c'));

        $php = escapeshellarg(PHP_BINARY);
        $spark = escapeshellarg(ROOTPATH . 'spark');
        $id = (int) $attachmentId;

        if (defined('PHP_OS_FAMILY') && PHP_OS_FAMILY === 'Windows') {
            $command = 'start /B "" ' . $php . ' ' . $spark . ' incident:convert-attachment-preview ' . $id;
        } else {
            $command = $php . ' ' . $spark . ' incident:convert-attachment-preview ' . $id . ' > /dev/null 2>&1 &';
        }

        @exec($command);
    }

    private function mapRow(array $row, int $index): array
    {
        $map = [
            'N' => 'n',
            'Month of Incident' => 'month_of_incident',
            'Year of Incident' => 'year_of_incident',
            'Year' => 'year_of_incident',
            'Province' => 'province',
            'Municipality/City where Incidence Occurred' => 'municipality',
            'Municipality/City where Incident Occurred' => 'municipality',
            'Municipality' => 'municipality',
            // primary single-column victim name
            'Name of Victim' => 'name_of_victim',
            // support spreadsheets that split the name into parts
            'Last Name' => 'last_name',
            'First Name' => 'first_name',
            'Middle Name' => 'middle_name',
            'Location Category' => 'location_category',
            'Age of the Person' => 'age',
            'Age' => 'age',
            'Sex' => 'gender',
            'Gender' => 'gender',
            // when client-side normalization runs, rows use the canonical header
            // coming from the table columns
            'Gender of the Person' => 'gender',
            'Occasion' => 'occasion',
            'Other Factors' => 'factors',
            'Factors' => 'factors',
            "Person's Residence" => 'residence',
            'Occupation of the Victim' => 'occupation',
            'Occupation' => 'occupation',
            'Remarks' => 'remarks',
            // Disregard Actions, Review, Attachments columns
            // 'Actions' => null,
            // 'Review' => null,
            // 'Attachments' => null,
        ];

        $data = [];
        foreach ($row as $key => $value) {
            if (!isset($map[$key])) {
                continue;
            }
            $field = $map[$key];
            $data[$field] = $value;
        }

        // If the sheet provided separate Last/First/Middle columns, combine them
        $last = $this->toString($data['last_name'] ?? '');
        $first = $this->toString($data['first_name'] ?? '');
        $middle = $this->toString($data['middle_name'] ?? '');
        if ($last !== '' || $first !== '' || $middle !== '') {
            $combined = trim(implode(' ', array_filter([$last, $first, $middle])));
            // prefer the explicitly combined parts over a single-column value
            $data['name_of_victim'] = $combined;
            unset($data['last_name'], $data['first_name'], $data['middle_name']);
        }

        $n = $this->toInt($data['n'] ?? null);
        $data['n'] = $n === null ? null : $n;
        $data['month_of_incident'] = $this->toString($data['month_of_incident'] ?? '');
        $data['year_of_incident'] = $this->toInt($data['year_of_incident'] ?? null);
        $data['province'] = $this->toString($data['province'] ?? '');
        $data['municipality'] = $this->toString($data['municipality'] ?? '');
        $data['name_of_victim'] = $this->toString($data['name_of_victim'] ?? '');
        $data['location_category'] = $this->toString($data['location_category'] ?? '');
        $data['age'] = $this->toInt($data['age'] ?? null);
        $data['gender'] = $this->normalizeGender($data['gender'] ?? '');
        // Accept 'Male'/'Female' as well as 'm'/'f' for Excel imports
        if (isset($data['gender'])) {
            $gender = strtolower(trim((string)$data['gender']));
            if ($gender === 'male') {
                $data['gender'] = 'm';
            } else if ($gender === 'female') {
                $data['gender'] = 'f';
            }
        }
        $data['occasion'] = $this->toString($data['occasion'] ?? '');
        $data['factors'] = $this->toString($data['factors'] ?? '');
        $data['residence'] = $this->toString($data['residence'] ?? '');
        $data['occupation'] = $this->toString($data['occupation'] ?? '');
        $data['remarks'] = $this->toString($data['remarks'] ?? '');

        return $data;
    }

    private function hashRow(array $row): string
    {
        $payload = [
            'month_of_incident' => $row['month_of_incident'] ?? '',
            'year_of_incident' => $row['year_of_incident'] ?? '',
            'province' => $row['province'] ?? '',
            'municipality' => $row['municipality'] ?? '',
            'name_of_victim' => $row['name_of_victim'] ?? '',
            'location_category' => $row['location_category'] ?? '',
            'age' => $row['age'] ?? '',
            'gender' => $row['gender'] ?? '',
            'occasion' => $row['occasion'] ?? '',
            'factors' => $row['factors'] ?? '',
            'residence' => $row['residence'] ?? '',
            'occupation' => $row['occupation'] ?? '',
            'remarks' => $row['remarks'] ?? '',
        ];

        return hash('sha256', json_encode($payload));
    }

    private function toString($value): string
    {
        if ($value === null) {
            return '';
        }

        return trim((string) $value);
    }

    private function toInt($value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return (int) $value;
        }

        return null;
    }

    private function normalizeGender($value): string
    {
        $value = strtolower($this->toString($value));

        if ($value === 'm' || $value === 'male') {
            return 'm';
        }

        if ($value === 'f' || $value === 'female') {
            return 'f';
        }

        return $value;
    }

    private function getNextIncidentNumber(): int
    {
        $row = $this->incidentReportModel->selectMax('n')->first();
        $current = isset($row['n']) ? (int) $row['n'] : 0;

        return $current + 1;
    }

    private function getMissingFields(array $data, array $required): array
    {
        $missing = [];
        foreach ($required as $field) {
            $value = $data[$field] ?? null;
            if ($value === null || $value === '') {
                $missing[] = $field;
            }
        }

        return $missing;
    }
}
