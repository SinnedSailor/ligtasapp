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
        'location_name',
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
        // newly added field for notes when an incident is rejected; previously
        // the controller would attempt to update this column but it wasn't
        // included in the allowedFields list so the value was silently dropped.
        'review_note',
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
            $plain = trim($data['data']['name_of_victim']);
            // deterministic lowercase value for hashing/searching
            $normalized = mb_strtolower($plain);
            $key = env('encryption.key') ?: (getenv('encryption.key') ?: 'CHANGE_ME__SET_ENCRYPTION_KEY');
            $h = hash_hmac('sha256', $normalized, $key);
            $data['data']['name_of_victim_hash'] = $h;

            // Store display-friendly Title Case in the encrypted column (reversible)
            $displayName = mb_convert_case($plain, MB_CASE_TITLE, 'UTF-8');
            $data['data']['name_of_victim_enc'] = base64_encode($encrypter->encrypt($displayName));

            // normalize plaintext key so callers see Title Case immediately
            $data['data']['name_of_victim'] = $displayName;
        }

        return $data;
    }

    protected $allowCallbacks = true;
    // callbacks run before insert/update operations
    protected $beforeInsert = ['encryptVictimName', 'normaliseLocationCategory'];
    protected $beforeUpdate = ['encryptVictimName', 'normaliseLocationCategory'];

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
            $plain = $this->decryptValue((string) $row['name_of_victim_enc']);
            $row['name_of_victim'] = $plain === null || $plain === '' ? $plain : mb_convert_case($plain, MB_CASE_TITLE, 'UTF-8');
        } elseif (isset($row['name_of_victim'])) {
            $plain = $this->decryptValue((string) $row['name_of_victim']);
            $row['name_of_victim'] = $plain === null || $plain === '' ? $plain : mb_convert_case($plain, MB_CASE_TITLE, 'UTF-8');
        }
        return $row;
    }

    /**
     * Ensure location categories are stored in a normalized form.  This keeps the
     * datalist suggestions tidy without requiring manual cleanup later.
     */
    /**
     * Clean up user-provided string fields so the stored values are consistent.
     *
     * Previously this only applied to `location_category`; expand it to also
     * normalise `occasion` when present.  Both values are trimmed and converted
     * to Title Case.
     */
    protected function normaliseLocationCategory(array $data)
    {
        if (! empty($data['data']['location_category'])) {
            $data['data']['location_category'] = trim(
                mb_convert_case($data['data']['location_category'], MB_CASE_TITLE, 'UTF-8')
            );
        }

        if (! empty($data['data']['occasion'])) {
            $data['data']['occasion'] = trim(
                mb_convert_case($data['data']['occasion'], MB_CASE_TITLE, 'UTF-8')
            );
        }

        if (! empty($data['data']['occupation'])) {
            $data['data']['occupation'] = trim(
                mb_convert_case($data['data']['occupation'], MB_CASE_TITLE, 'UTF-8')
            );
        }

        return $data;
    }

    /**
     * Prepare incident data for save when callers provide plaintext `name_of_victim`.
     * This runs the same encryption/hash logic used by callbacks and removes the
     * plaintext key so the DB only receives `name_of_victim_enc`/`name_of_victim_hash`.
     */
    public function prepareForInsert(array $data): array
    {
        $wrapped = ['data' => $data];
        $wrapped = $this->encryptVictimName($wrapped);
        $data = $wrapped['data'] ?? [];

        // Remove plaintext key so it is not sent to the database (column was dropped)
        unset($data['name_of_victim']);

        return $data;
    }

    /**
     * Return a sorted list of non-empty distinct location_category values stored
     * in the database.  Used for populating suggestion lists on the client side.
     *
     * @return string[]
     */
    public function getDistinctLocationCategories(): array
    {
        $builder = $this->builder();
        $builder->select('location_category')
                ->distinct()
                ->where('location_category IS NOT NULL', null, false)
                ->where('location_category !=', '')
                ->orderBy('location_category', 'asc');

        $result = $builder->get()->getResultArray();
        return array_map(fn($r) => $r['location_category'], $result);
    }

    /**
     * Like getDistinctLocationCategories but for the `occasion` column.  Used to
     * preload suggestions for the form.
     */
    public function getDistinctOccasions(): array
    {
        $builder = $this->builder();
        $builder->select('occasion')
                ->distinct()
                ->where('occasion IS NOT NULL', null, false)
                ->where('occasion !=', '')
                ->orderBy('occasion', 'asc');

        $result = $builder->get()->getResultArray();
        return array_map(fn($r) => $r['occasion'], $result);
    }

    /**
     * Return sorted non-empty distinct occupation values stored in database.
     */
    public function getDistinctOccupations(): array
    {
        $builder = $this->builder();
        $builder->select('occupation')
                ->distinct()
                ->where('occupation IS NOT NULL', null, false)
                ->where('occupation !=', '')
                ->orderBy('occupation', 'asc');

        $result = $builder->get()->getResultArray();
        return array_map(fn($r) => $r['occupation'], $result);
    }

    /**
     * Return sorted non-empty distinct factors values stored in database.
     */
    public function getDistinctFactors(): array
    {
        $builder = $this->builder();
        $builder->select('factors')
                ->distinct()
                ->where('factors IS NOT NULL', null, false)
                ->where('factors !=', '')
                ->orderBy('factors', 'asc');

        $result = $builder->get()->getResultArray();
        return array_map(fn($r) => $r['factors'], $result);
    }
}
