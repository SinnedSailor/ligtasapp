<?php

namespace App\Models;

use CodeIgniter\Model;

class DocumentModel extends Model
{
    protected $table = 'documents';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'user_id',
        'doc_type',
        'original_name',
        'stored_name',
        'stored_path',
        'preview_path',
        'preview_mime',
        'mime_type',
        'size_bytes',
        'status',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getDocumentsForUser(int $userId): array
    {
        $rows = $this->select('documents.*, users.first_name_enc, users.last_name_enc, users.province, users.municipality')
            ->join('users', 'users.id = documents.user_id')
            ->where('documents.user_id', $userId)
            ->orderBy('documents.created_at', 'desc')
            ->findAll();

        $userModel = new \App\Models\UserModel();
        foreach ($rows as &$r) {
            $r['first_name'] = $userModel->decryptValue($r['first_name_enc'] ?? '');
            $r['last_name']  = $userModel->decryptValue($r['last_name_enc'] ?? '');
        }
        unset($r);

        return $rows;
    }

    public function getDocumentsByStatus(string $status): array
    {
        $rows = $this->select('documents.*, users.first_name_enc, users.last_name_enc, users.province, users.municipality')
            ->join('users', 'users.id = documents.user_id')
            ->where('documents.status', $status)
            ->orderBy('documents.created_at', 'desc')
            ->findAll();

        $userModel = new \App\Models\UserModel();
        foreach ($rows as &$r) {
            $r['first_name'] = $userModel->decryptValue($r['first_name_enc'] ?? '');
            $r['last_name']  = $userModel->decryptValue($r['last_name_enc'] ?? '');
        }
        unset($r);

        return $rows;
    }

    public function getRecentDocumentsForUser(int $userId, string $docType, int $minutes): array
    {
        $since = date('Y-m-d H:i:s', time() - ($minutes * 60));

        return $this->where('documents.user_id', $userId)
            ->where('documents.doc_type', $docType)
            ->where('documents.created_at >=', $since)
            ->orderBy('documents.created_at', 'desc')
            ->findAll();
    }
}
