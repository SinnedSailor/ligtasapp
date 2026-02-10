<?php

namespace App\Commands;

use App\Models\IncidentReportAttachmentModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class ConvertIncidentAttachmentPreview extends BaseCommand
{
    protected $group = 'Incident';
    protected $name = 'incident:convert-attachment-preview';
    protected $description = 'Convert an incident attachment preview to PDF in the background.';

    public function run(array $params)
    {
        $attachmentId = (int) ($params[0] ?? 0);
        if ($attachmentId <= 0) {
            CLI::error('Attachment ID is required.');
            return;
        }

        $model = new IncidentReportAttachmentModel();
        $attachment = $model->find($attachmentId);
        if (!$attachment) {
            CLI::error('Attachment not found.');
            $this->clearQueueLock($attachmentId);
            return;
        }

        if (!in_array($attachment['mime_type'] ?? '', [
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ], true)) {
            $this->clearQueueLock($attachmentId);
            return;
        }

        $sourcePath = WRITEPATH . 'uploads/' . ($attachment['stored_path'] ?? '');
        if (!is_file($sourcePath)) {
            CLI::error('Source file not found.');
            $this->clearQueueLock($attachmentId);
            return;
        }

        $previewDir = WRITEPATH . 'uploads/incident_reports/previews';
        if (!is_dir($previewDir)) {
            mkdir($previewDir, 0775, true);
        }

        $storedName = $attachment['stored_name'] ?? '';
        $outputName = pathinfo($storedName, PATHINFO_FILENAME) . '.pdf';
        $outputPath = $previewDir . DIRECTORY_SEPARATOR . $outputName;

        $soffice = $this->getSofficePath();
        if ($soffice === null) {
            CLI::error('LibreOffice (soffice) not found.');
            $this->clearQueueLock($attachmentId);
            return;
        }

        $command = escapeshellarg($soffice)
            . ' --headless --nologo --nofirststartwizard --convert-to pdf --outdir '
            . escapeshellarg($previewDir) . ' ' . escapeshellarg($sourcePath);

        $output = [];
        $exitCode = 1;
        @exec($command, $output, $exitCode);

        if ($exitCode === 0 && is_file($outputPath)) {
            $model->update($attachmentId, [
                'preview_path' => 'incident_reports/previews/' . $outputName,
                'preview_mime' => 'application/pdf',
            ]);
        } else {
            CLI::error('Preview conversion failed.');
        }

        $this->clearQueueLock($attachmentId);
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

    private function clearQueueLock(int $attachmentId): void
    {
        $lockPath = WRITEPATH . 'uploads/incident_reports/previews/.queue-' . $attachmentId;
        if (is_file($lockPath)) {
            @unlink($lockPath);
        }
    }
}
