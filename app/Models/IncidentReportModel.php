<?php

namespace App\Models;

use CodeIgniter\Model;

class IncidentReportModel extends Model
{
    protected $table = 'incident_reports';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'row_hash',
        'n',
        'month_of_incident',
        'year_of_incident',
        'province',
        'municipality',
        'name_of_victim_enc',
        'name_of_victim_hash',
        'location_category',
        'age',
        'gender',
        'occasion',
        'factors',
        'residence',
        'region',
        'occupation',
        'remarks',
        'review_status',
        'reviewed_by',
        'reviewed_at',
    ];

    // Ensure victim name is hashed for new/updated records
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected function encryptVictimName(array $data)
    {
        $encrypter = \Config\Services::encrypter();

        if (!empty($data['data']['name_of_victim'])) {
            $name = mb_strtolower(trim($data['data']['name_of_victim']));
            // deterministic hash for dedup/search
            $key = env('encryption.key') ?: (getenv('encryption.key') ?: 'CHANGE_ME__SET_ENCRYPTION_KEY');
            $h = hash_hmac('sha256', $name, $key);
            $data['data']['name_of_victim_hash'] = $h;
            // store encrypted ciphertext in the name_of_victim_enc column (base64)
            $data['data']['name_of_victim_enc'] = base64_encode($encrypter->encrypt($name));
        }

        return $data;
    }

    protected $allowCallbacks = true;
    protected $beforeInsert = ['encryptVictimName'];
    protected $beforeUpdate = ['encryptVictimName'];

    public function decryptValue(?string $value): ?string
    {
        if (empty($value)) {
            return $value;
        }

        $encrypter = \Config\Services::encrypter();
        try {
            $decoded = base64_decode($value, true);
            if ($decoded === false) {
                return $value;
            }
            $plain = $encrypter->decrypt($decoded);
            return $plain === false ? $value : $plain;
        } catch (\Throwable $e) {
            return $value;
        }
    }

    public function decryptRow(array $row): array
    {
        if (isset($row['name_of_victim_enc']) && $row['name_of_victim_enc'] !== null) {
            $row['name_of_victim'] = $this->decryptValue((string) $row['name_of_victim_enc']);
        } elseif (isset($row['name_of_victim'])) {
            $row['name_of_victim'] = $this->decryptValue((string) $row['name_of_victim']);
        }
        return $row;
    }
}
