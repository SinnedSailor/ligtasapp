<?php

namespace App\Commands;

use App\Models\DocumentModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class ConvertDocumentPreview extends BaseCommand
{
    protected $group = 'Documents';
    protected $name = 'documents:convert-preview';
    protected $description = 'Convert a document preview to PDF in the background.';

    public function run(array $params)
    {
        $documentId = (int) ($params[0] ?? 0);
        if ($documentId <= 0) {
            CLI::error('Document ID is required.');
            return;
        }

        $model = new DocumentModel();
        $document = $model->find($documentId);
        if (!$document) {
            CLI::error('Document not found.');
            $this->clearQueueLock($documentId);
            return;
        }

        if (!in_array($document['mime_type'] ?? '', [
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ], true)) {
            $this->clearQueueLock($documentId);
            return;
        }

        $sourcePath = WRITEPATH . 'uploads/' . ($document['stored_path'] ?? '');
        if (!is_file($sourcePath)) {
            CLI::error('Source file not found.');
            $this->clearQueueLock($documentId);
            return;
        }

        $previewDir = WRITEPATH . 'uploads/documents/previews';
        if (!is_dir($previewDir)) {
            mkdir($previewDir, 0775, true);
        }

        $storedName = $document['stored_name'] ?? '';
        $outputName = pathinfo($storedName, PATHINFO_FILENAME) . '.pdf';
        $outputPath = $previewDir . DIRECTORY_SEPARATOR . $outputName;

        $soffice = $this->getSofficePath();
        if ($soffice === null) {
            CLI::error('LibreOffice (soffice) not found.');
            $this->clearQueueLock($documentId);
            return;
        }

        $command = escapeshellarg($soffice)
            . ' --headless --nologo --nofirststartwizard --convert-to pdf --outdir '
            . escapeshellarg($previewDir) . ' ' . escapeshellarg($sourcePath);

        $output = [];
        $exitCode = 1;
        @exec($command, $output, $exitCode);

        if ($exitCode === 0 && is_file($outputPath)) {
            $model->update($documentId, [
                'preview_path' => 'documents/previews/' . $outputName,
                'preview_mime' => 'application/pdf',
            ]);
        } else {
            CLI::error('Preview conversion failed.');
        }

        $this->clearQueueLock($documentId);
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

            return null;
        }

        return 'soffice';
    }

    private function clearQueueLock(int $documentId): void
    {
        $lockPath = WRITEPATH . 'uploads/documents/previews/.queue-' . $documentId;
        if (is_file($lockPath)) {
            @unlink($lockPath);
        }
    }
}
