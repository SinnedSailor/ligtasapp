<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'first_name_enc',
        'last_name_enc',
        'username',
        'email_hash',
        'email_enc',
        'password',
        'province',
        'municipality',
        'contact_number_enc',
        'role_id',
        'is_admin',
        'OTP',
        'OTP_EXPIRED',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    // NOTE: uniqueness for email is enforced on the `email` column (it stores the deterministic hash).
    protected $validationRules = [
        'first_name' => 'required|min_length[2]|max_length[100]',
        'last_name' => 'required|min_length[2]|max_length[100]',
        'username' => 'required|min_length[3]|max_length[50]|is_unique[users.username]',
        // Validate email format here; uniqueness is checked via model lookup before insert
        'email' => 'required|valid_email',
        'password' => 'required|min_length[8]',
    ];

    protected $validationMessages = [
        'username' => [
            'is_unique' => 'This username is already taken.'
        ],
        'email' => [
            'is_unique' => 'This email is already registered.'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    // keep password hashing, and encrypt PII before save (reversible)
    protected $beforeInsert = ['hashPassword', 'encryptPII'];
    protected $beforeUpdate = ['hashPassword', 'encryptPII'];

    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }
        return $data;
    }

    /**
     * Encrypt personally-identifying fields (reversible) and store deterministic
     * email hash for lookup. Encrypted values are base64-encoded ciphertext.
     */
    protected function encryptPII(array $data)
    {
        $encrypter = \Config\Services::encrypter();

        if (!empty($data['data']['first_name'])) {
            // Store first name in Title Case (capitalize first letter of each word)
            $plainFirst = mb_convert_case(trim($data['data']['first_name']), MB_CASE_TITLE, 'UTF-8');
            $data['data']['first_name_enc'] = base64_encode($encrypter->encrypt($plainFirst));
            // normalize plaintext key so callers see Title Case immediately
            $data['data']['first_name'] = $plainFirst;
        }

        if (!empty($data['data']['last_name'])) {
            // Store last name in Title Case (capitalize first letter of each word)
            $plainLast = mb_convert_case(trim($data['data']['last_name']), MB_CASE_TITLE, 'UTF-8');
            $data['data']['last_name_enc'] = base64_encode($encrypter->encrypt($plainLast));
            $data['data']['last_name'] = $plainLast;
        }

        if (!empty($data['data']['contact_number'])) {
            $data['data']['contact_number_enc'] = base64_encode($encrypter->encrypt(trim($data['data']['contact_number'])));
        }

        if (!empty($data['data']['email'])) {
            $emailNorm = mb_strtolower(trim($data['data']['email']));
            // deterministic hash for lookup/auth
            $emailHash = $this->hashValue($emailNorm);
            // store encrypted ciphertext in `email_enc`
            $data['data']['email_enc'] = base64_encode($encrypter->encrypt($emailNorm));
            // store deterministic hash in `email_hash` (used for lookup/indexing)
            $data['data']['email_hash'] = $emailHash;
        }

        return $data;
    }

    /**
     * Prepare user data for insertion when controllers have plaintext fields but
     * the database only stores encrypted PII. This ensures encrypted columns
     * are created and plaintext keys removed before insert.
     */
    public function prepareForInsert(array $data): array
    {
        $wrapped = ['data' => $data];
        $wrapped = $this->encryptPII($wrapped);
        $data = $wrapped['data'] ?? [];

        // Remove plaintext PII keys so they are not sent to DB (columns were removed)
        unset($data['first_name'], $data['last_name'], $data['email'], $data['contact_number']);

        return $data;
    }

    protected function hashValue(string $value): string
    {
        $key = env('encryption.key') ?: (getenv('encryption.key') ?: 'CHANGE_ME__SET_ENCRYPTION_KEY');
        return hash_hmac('sha256', mb_strtolower(trim($value)), $key);
    }

    public function getUserByEmail($email)
    {
        if (empty($email)) {
            return null;
        }

        $hash = $this->hashValue($email);
        return $this->where('email_hash', $hash)->first();
    }

    public function getUserByUsername($username)
    {
        return $this->where('username', $username)->first();
    }

    /**
     * Try to decrypt a base64-encoded ciphertext value. If decryption fails,
     * return the original value.
     */
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

    /**
     * Decrypt user fields in-place for display/use. Leaves fields unchanged
     * if they cannot be decrypted.
     */
    /**
     * Decrypt user fields in-place for display/use. Leaves fields unchanged
     * if they cannot be decrypted.
     *
     * @param array $user
     * @param bool  $revealEmail  when true, attempt to expose the decrypted
     *                            email plaintext instead of always returning a
     *                            hash.  Defaults to false for safety.
     */
    public function decryptUserRow(array $user, bool $revealEmail = false): array
    {
        // Prefer the new encrypted columns when available
        if (isset($user['first_name_enc']) && $user['first_name_enc']) {
            $user['first_name'] = $this->decryptValue((string) $user['first_name_enc']);
        } elseif (isset($user['first_name'])) {
            $user['first_name'] = $this->decryptValue((string) $user['first_name']);
        }

        if (isset($user['last_name_enc']) && $user['last_name_enc']) {
            $user['last_name'] = $this->decryptValue((string) $user['last_name_enc']);
        } elseif (isset($user['last_name'])) {
            $user['last_name'] = $this->decryptValue((string) $user['last_name']);
        }

        // Email handling: default behaviour is to return the deterministic hash
        // (either stored or computed) so plaintext isn't leaked.  If callers
        // explicitly request the plaintext, we decrypt the encrypted column.
        if (isset($user['email_enc']) && $user['email_enc']) {
            $plain = $this->decryptValue((string) $user['email_enc']);
            if ($revealEmail && $plain) {
                $user['email'] = $plain;
            } else {
                $user['email'] = $plain ? $this->hashValue($plain) : ($user['email_hash'] ?? ($user['email'] ?? null));
            }
        } elseif (isset($user['email_hash'])) {
            $user['email'] = $user['email_hash'];
        } elseif (isset($user['email'])) {
            // legacy fallback: if `email` field exists but contains plaintext, replace with hash
            if (!preg_match('/^[0-9a-f]{64}$/i', (string) $user['email'])) {
                $user['email'] = $this->hashValue((string) $user['email']);
            }
        }

        if (isset($user['contact_number_enc']) && $user['contact_number_enc']) {
            $user['contact_number'] = $this->decryptValue((string) $user['contact_number_enc']);
        } elseif (isset($user['contact_number'])) {
            $user['contact_number'] = $this->decryptValue((string) $user['contact_number']);
        }

        // Ensure API always includes these keys so front-end won't receive `undefined`.
        // Use empty string when PII is not present/encrypted.
        $user['first_name'] = $user['first_name'] ?? '';
        $user['last_name']  = $user['last_name'] ?? '';
        $user['email']      = $user['email'] ?? '';
        $user['contact_number'] = $user['contact_number'] ?? '';

        return $user;

        

    }

    public function insert_otp(int $userId, array $data): bool
    {
        return $this->update($userId, [
            'OTP'         => $data['otp'] ?? ($data['OTP'] ?? null),
            'OTP_EXPIRED' => $data['otp_expiration'] ?? ($data['OTP_EXPIRED'] ?? null),
        ]);
    }

    public function verify_otp(int $userId, string $otp): bool
    {
        $user = $this->select('OTP, OTP_EXPIRED')->find($userId);
        if (! $user) {
            return false;
        }
        if ((string) ($user['OTP'] ?? '') !== trim($otp)) {
            return false;
        }
        if (empty($user['OTP_EXPIRED']) || strtotime($user['OTP_EXPIRED']) < time()) {
            return false;
        }
        // Clear OTP after successful verification
        $this->update($userId, ['OTP' => null, 'OTP_EXPIRED' => null]);
        return true;
    }

}
