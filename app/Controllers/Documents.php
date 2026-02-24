<?php

namespace App\Controllers;

use App\Models\DocumentModel;

class Documents extends BaseController
{
    protected DocumentModel $documentModel;

    public function __construct()
    {
        $this->documentModel = new DocumentModel();
    }

    private function getNormalizedRoleName(): string
    {
        $roleName = strtoupper(trim((string) session()->get('role_name')));

        return $roleName !== '' ? $roleName : 'NO ROLE';
    }

    public function index()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $roleName = $this->getNormalizedRoleName();
        $isAdmin = (bool) session()->get('is_admin');
        $userId = (int) session()->get('user_id');

        $data = [
            'roleName' => $roleName,
            'isAdmin' => $isAdmin,
            'myDocuments' => [],
            'pendingDocuments' => [],
            'approvedDocuments' => [],
            'provinces' => $this->getRegion1Provinces(),
            'municipalities' => $this->getRegion1Municipalities(),
        ];

        if ($roleName === 'LGU') {
            $data['myDocuments'] = $this->documentModel->getDocumentsForUser($userId);
        }

        if ($roleName === 'PROVINCE' || $isAdmin) {
            $data['pendingDocuments'] = $this->documentModel->getDocumentsByStatus('pending');
        }

        if ($roleName === 'FOCAL' || $isAdmin) {
            $data['approvedDocuments'] = $this->documentModel->getDocumentsByStatus('approved');
        }

        return view('documents', $data);
    }

    public function upload()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $roleName = $this->getNormalizedRoleName();
        if ($roleName !== 'LGU') {
            return redirect()->to('/documents')->with('error', 'Only LGU users can upload documents.');
        }

        $filesByType = [
            'ordinance' => $this->request->getFileMultiple('ordinance_files'),
            'pops' => $this->request->getFileMultiple('pops_files'),
            'budget' => $this->request->getFileMultiple('budget_files'),
        ];

        $allowedTypes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ];

        $maxSize = 10 * 1024 * 1024;
        $uploadedCount = 0;
        $duplicateCount = 0;
        $dedupeMinutes = 2;
        $userId = (int) session()->get('user_id');
        $seenHashesByType = [
            'ordinance' => [],
            'pops' => [],
            'budget' => [],
        ];

        foreach ($filesByType as $docType => $files) {
            if (!$files) {
                continue;
            }

            $recentDocs = $this->documentModel->getRecentDocumentsForUser($userId, $docType, $dedupeMinutes);

            foreach ($files as $file) {
                if (!$file || !$file->isValid() || $file->hasMoved()) {
                    continue;
                }

                if (!in_array($file->getClientMimeType(), $allowedTypes, true)) {
                    continue;
                }

                if ($file->getSize() > $maxSize) {
                    continue;
                }

                $uploadDir = WRITEPATH . 'uploads/documents/' . $docType;
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0775, true);
                }

                $storedName = $file->getRandomName();
                $file->move($uploadDir, $storedName);

                $fullPath = $uploadDir . DIRECTORY_SEPARATOR . $storedName;
                $previewPath = null;
                $previewMime = null;

                $newHash = @hash_file('sha256', $fullPath) ?: null;
                if ($newHash && in_array($newHash, $seenHashesByType[$docType], true)) {
                    @unlink($fullPath);
                    $duplicateCount++;
                    continue;
                }

                if ($newHash) {
                    $isDuplicate = false;
                    foreach ($recentDocs as $recentDoc) {
                        $recentPath = WRITEPATH . 'uploads/' . ($recentDoc['stored_path'] ?? '');
                        if (!is_file($recentPath)) {
                            continue;
                        }

                        $recentHash = @hash_file('sha256', $recentPath) ?: null;
                        if ($recentHash && $recentHash === $newHash) {
                            $isDuplicate = true;
                            break;
                        }
                    }

                    if ($isDuplicate) {
                        @unlink($fullPath);
                        $duplicateCount++;
                        continue;
                    }
                }

                $documentId = $this->documentModel->insert([
                    'user_id' => $userId,
                    'doc_type' => $docType,
                    'original_name' => $file->getClientName(),
                    'stored_name' => $storedName,
                    'stored_path' => 'documents/' . $docType . '/' . $storedName,
                    'preview_path' => $previewPath,
                    'preview_mime' => $previewMime,
                    'mime_type' => $file->getClientMimeType(),
                    'size_bytes' => $file->getSize(),
                    'status' => 'pending',
                ], true);

                if ($documentId && $this->isOfficeDocument(['mime_type' => $file->getClientMimeType()])) {
                    $this->queuePreviewConversion((int) $documentId);
                }

                if ($newHash) {
                    $seenHashesByType[$docType][] = $newHash;
                }

                $uploadedCount++;
            }
        }

        if ($uploadedCount === 0 && $duplicateCount > 0) {
            return redirect()->to('/documents')->with('error', 'Duplicate upload detected. No new documents were added.');
        }

        if ($uploadedCount === 0) {
            return redirect()->to('/documents')->with('error', 'No valid documents were uploaded.');
        }

        if ($duplicateCount > 0) {
            return redirect()->to('/documents')->with('success', 'Documents submitted successfully. Duplicate files were skipped.');
        }

        return redirect()->to('/documents')->with('success', 'Documents submitted successfully and are pending review.');
    }

    public function approve(int $documentId)
    {
        return $this->updateStatus($documentId, 'approved');
    }

    public function reject(int $documentId)
    {
        return $this->updateStatus($documentId, 'rejected');
    }

    private function updateStatus(int $documentId, string $status)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $roleName = $this->getNormalizedRoleName();
        $isAdmin = (bool) session()->get('is_admin');
        if ($roleName !== 'PROVINCE' && !$isAdmin) {
            return redirect()->to('/documents')->with('error', 'You do not have permission to review documents.');
        }

        $document = $this->documentModel->find($documentId);
        if (!$document) {
            return redirect()->to('/documents')->with('error', 'Document not found.');
        }

        if ($document['status'] !== 'pending') {
            return redirect()->to('/documents')->with('error', 'Only pending documents can be reviewed.');
        }

        $this->documentModel->update($documentId, [
            'status' => $status,
            'reviewed_by' => (int) session()->get('user_id'),
            'reviewed_at' => date('Y-m-d H:i:s'),
        ]);

        $message = $status === 'approved' ? 'Document approved.' : 'Document rejected.';
        return redirect()->to('/documents')->with('success', $message);
    }

    public function download(int $documentId)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $document = $this->documentModel->find($documentId);
        if (!$document) {
            return redirect()->to('/documents')->with('error', 'Document not found.');
        }

        $roleName = $this->getNormalizedRoleName();
        $isAdmin = (bool) session()->get('is_admin');
        $userId = (int) session()->get('user_id');

        $canDownload = false;
        $documentOwnerId = (int) ($document['user_id'] ?? 0);
        if ($isAdmin) {
            $canDownload = true;
        } elseif ($roleName === 'LGU' && $documentOwnerId === $userId) {
            $canDownload = true;
        } elseif ($roleName === 'PROVINCE') {
            $canDownload = true;
        } elseif ($roleName === 'FOCAL' && $document['status'] === 'approved') {
            $canDownload = true;
        }

        if (!$canDownload) {
            return redirect()->to('/documents')->with('error', 'You do not have permission to access this file.');
        }

        $fullPath = WRITEPATH . 'uploads/' . $document['stored_path'];
        if (!is_file($fullPath)) {
            return redirect()->to('/documents')->with('error', 'File not found on server.');
        }

        return $this->response->download($fullPath, null)->setFileName($document['original_name']);
    }

    public function view(int $documentId)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $document = $this->documentModel->find($documentId);
        if (!$document) {
            return redirect()->to('/documents')->with('error', 'Document not found.');
        }

        $roleName = $this->getNormalizedRoleName();
        $isAdmin = (bool) session()->get('is_admin');
        $userId = (int) session()->get('user_id');


        $canView = false;
        $documentOwnerId = (int) ($document['user_id'] ?? 0);
        if ($isAdmin) {
            $canView = true;
        } elseif ($roleName === 'LGU' && $documentOwnerId === $userId) {
            $canView = true;
        } elseif ($roleName === 'PROVINCE') {
            $canView = true;
        } elseif ($roleName === 'FOCAL' && $document['status'] === 'approved') {
            $canView = true;
        }

        if (!$canView) {
            return redirect()->to('/documents')->with('error', 'You do not have permission to access this file.');
        }

        $previewPath = $document['preview_path'] ?? null;
        $previewMime = $document['preview_mime'] ?? null;
        $filePath = null;
        $mimeType = null;

        if ($previewPath) {
            $previewFullPath = WRITEPATH . 'uploads/' . $previewPath;
            if (is_file($previewFullPath)) {
                $filePath = $previewFullPath;
                $mimeType = $previewMime ?: 'application/pdf';
            }
        }

        $originalPath = WRITEPATH . 'uploads/' . $document['stored_path'];
        if (!$filePath) {
            if (is_file($originalPath) && $document['mime_type'] === 'application/pdf') {
                $filePath = $originalPath;
                $mimeType = 'application/pdf';
            }
        }

        if (!$filePath && is_file($originalPath) && $this->isOfficeDocument($document)) {
            $this->queuePreviewConversion($documentId);

            if ($this->isPreviewConversionQueued($documentId)) {
                return $this->response
                    ->setHeader('Content-Type', 'text/html; charset=UTF-8')
                    ->setBody('<div style="padding:16px;font-family:Arial,sans-serif;">Converting preview... please wait.<script>setTimeout(function(){location.reload();}, 3000);</script></div>');
            }
        }

        if (!$filePath) {
            if (is_file($originalPath)) {
                return $this->response
                    ->setHeader('Content-Type', 'text/html; charset=UTF-8')
                    ->setBody('<div style="padding:16px;font-family:Arial,sans-serif;">Preview is not available for this file type. Please use Download.</div>');
            }

            return redirect()->to('/documents')->with('error', 'File not found on server.');
        }

        $contents = file_get_contents($filePath);

        return $this->response
            ->setHeader('Content-Type', $mimeType)
            ->setHeader('Content-Disposition', 'inline; filename="' . addslashes($document['original_name']) . '"')
            ->setBody($contents);
    }

    public function healthCheck()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON([
                'ok' => false,
                'message' => 'Authentication required.',
            ]);
        }

        if (!(bool) session()->get('is_admin')) {
            return $this->response->setStatusCode(403)->setJSON([
                'ok' => false,
                'message' => 'Admin access required.',
            ]);
        }

        $soffice = $this->getSofficePath();
        if ($soffice === null) {
            return $this->response->setStatusCode(500)->setJSON([
                'ok' => false,
                'message' => 'LibreOffice (soffice) not found.',
            ]);
        }

        $healthDir = WRITEPATH . 'uploads/documents/health';
        if (!is_dir($healthDir)) {
            mkdir($healthDir, 0775, true);
        }

        $timestamp = date('Ymd_His');
        $sourcePath = $healthDir . DIRECTORY_SEPARATOR . 'health_' . $timestamp . '.html';
        $outputName = 'health_' . $timestamp . '.pdf';
        $outputPath = $healthDir . DIRECTORY_SEPARATOR . $outputName;

        $html = '<html><body><p>IWAS health check</p></body></html>';
        file_put_contents($sourcePath, $html);

        $command = escapeshellarg($soffice)
            . ' --headless --nologo --nofirststartwizard --convert-to pdf --outdir '
            . escapeshellarg($healthDir) . ' ' . escapeshellarg($sourcePath);

        $output = [];
        $exitCode = 1;
        @exec($command, $output, $exitCode);

        $success = $exitCode === 0 && is_file($outputPath);

        if (is_file($sourcePath)) {
            @unlink($sourcePath);
        }
        if (is_file($outputPath)) {
            @unlink($outputPath);
        }

        if (!$success) {
            return $this->response->setStatusCode(500)->setJSON([
                'ok' => false,
                'message' => 'LibreOffice conversion failed.',
                'exit_code' => $exitCode,
                'soffice_path' => $soffice,
            ]);
        }

        return $this->response->setJSON([
            'ok' => true,
            'message' => 'LibreOffice conversion succeeded.',
            'soffice_path' => $soffice,
        ]);
    }

    private function convertToPdfPreview(string $sourcePath, string $storedName): ?string
    {
        if (!function_exists('exec')) {
            return null;
        }

        $soffice = $this->getSofficePath();
        if ($soffice === null) {
            log_message('warning', 'LibreOffice (soffice) not found for document preview conversion.');
            return null;
        }
        $previewDir = WRITEPATH . 'uploads/documents/previews';
        if (!is_dir($previewDir)) {
            mkdir($previewDir, 0775, true);
        }

        $outputName = pathinfo($storedName, PATHINFO_FILENAME) . '.pdf';
        $outputPath = $previewDir . DIRECTORY_SEPARATOR . $outputName;

        $command = escapeshellarg($soffice)
            . ' --headless --nologo --nofirststartwizard --convert-to pdf --outdir '
            . escapeshellarg($previewDir) . ' ' . escapeshellarg($sourcePath);

        $output = [];
        $exitCode = 1;
        @exec($command, $output, $exitCode);

        if ($exitCode !== 0 || !is_file($outputPath)) {
            log_message('warning', 'Document preview conversion failed', [
                'source' => $sourcePath,
                'output' => $outputPath,
                'exit_code' => $exitCode,
                'command' => $command,
            ]);
            return null;
        }

        return 'documents/previews/' . $outputName;
    }

    private function isOfficeDocument(array $document): bool
    {
        return in_array($document['mime_type'] ?? '', [
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ], true);
    }

    private function isPreviewConversionQueued(int $documentId): bool
    {
        return is_file($this->getPreviewQueueLockPath($documentId));
    }

    private function getPreviewQueueLockPath(int $documentId): string
    {
        return WRITEPATH . 'uploads/documents/previews/.queue-' . $documentId;
    }

    private function queuePreviewConversion(int $documentId): void
    {
        if (!function_exists('exec')) {
            return;
        }

        $previewDir = WRITEPATH . 'uploads/documents/previews';
        if (!is_dir($previewDir)) {
            mkdir($previewDir, 0775, true);
        }

        $lockPath = $this->getPreviewQueueLockPath($documentId);
        if (is_file($lockPath)) {
            return;
        }

        @file_put_contents($lockPath, date('c'));

        $php = escapeshellarg(PHP_BINARY);
        $spark = escapeshellarg(ROOTPATH . 'spark');
        $docId = (int) $documentId;

        if (defined('PHP_OS_FAMILY') && PHP_OS_FAMILY === 'Windows') {
            $command = 'start /B "" ' . $php . ' ' . $spark . ' documents:convert-preview ' . $docId;
        } else {
            $command = $php . ' ' . $spark . ' documents:convert-preview ' . $docId . ' > /dev/null 2>&1 &';
        }

        @exec($command);
    }

    private function getSofficePath(): ?string
    {
        $envPath = trim((string) getenv('SOFFICE_PATH'));
        if ($envPath !== '') {
            return $envPath;
        }

        if (defined('PHP_OS_FAMILY') && PHP_OS_FAMILY === 'Windows') {
            $candidates = [
                'C:\\Program Files\\LibreOffice\\program\\soffice.exe',
                'C:\\Program Files (x86)\\LibreOffice\\program\\soffice.exe',
            ];

            foreach ($candidates as $candidate) {
                if (is_file($candidate)) {
                    return $candidate;
                }
            }
        }

        return 'soffice';
    }
}
