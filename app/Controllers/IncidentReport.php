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

        // Remove fields that should not be updated
        unset($data['n']);
        unset($data['id']);

        $this->incidentReportModel->update($incident['id'], $data);

        return $this->response->setJSON([
            'message' => 'Incident updated successfully.'
        ]);
    }

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

            $mapped = $this->mapRow($row, $index);
            if ($mapped['n'] === null) {
                $mapped['n'] = $nextN;
                $nextN++;
            } elseif ($mapped['n'] >= $nextN) {
                $nextN = $mapped['n'] + 1;
            }
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
                ->where('n', $mapped['n'])
                ->orWhere('row_hash', $mapped['row_hash'])
                ->first();

            if ($existingRow) {
                $this->incidentReportModel->update($existingRow['id'], $mapped);
                $updated++;
                continue;
            }

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

        return $this->response->setJSON([
            'attachments' => $attachments,
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

        $contents = file_get_contents($filePath);

        return $this->response
            ->setHeader('Content-Type', $mimeType)
            ->setHeader('Content-Disposition', 'inline; filename="' . addslashes($attachment['original_name']) . '"')
            ->setBody($contents);
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
            'Name of Victim' => 'name_of_victim',
            'Location Category' => 'location_category',
            'Age of the Person' => 'age',
            'Age' => 'age',
            'Sex' => 'gender',
            'Gender' => 'gender',
            'Occasion' => 'occasion',
            'Other Factors' => 'factors',
            'Factors' => 'factors',
            "Person's Residence" => 'residence',
            'Occupation of the Victim' => 'occupation',
            'Occupation' => 'occupation',
            'Remarks' => 'remarks',
        ];

        $data = [];
        foreach ($row as $key => $value) {
            if (!isset($map[$key])) {
                continue;
            }
            $field = $map[$key];
            $data[$field] = $value;
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
