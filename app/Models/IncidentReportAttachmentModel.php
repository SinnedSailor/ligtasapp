<?php

namespace App\Models;

use CodeIgniter\Model;

class IncidentReportAttachmentModel extends Model
{
    protected $table = 'incident_report_attachments';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'incident_n',
        'file_kind',
        'original_name',
        'stored_name',
        'stored_path',
        'preview_path',
        'preview_mime',
        'mime_type',
        'size_bytes',
        'uploaded_by',
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}
