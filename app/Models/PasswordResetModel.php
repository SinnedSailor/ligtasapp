<?php

namespace App\Models;

use CodeIgniter\Model;

class PasswordResetModel extends Model
{
    protected $table            = 'password_resets';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'user_id',
        'email_hash',
        'token_hash',
        'expires_at',
        'created_at',
    ];

    protected $useTimestamps = false;

    /**
     * Create a new secure reset token for the specified user and email hash.
     * Deletes any prior tokens for the user.
     */
    public function createToken(int $userId, string $emailHash): string
    {
        $this->where('user_id', $userId)->delete();

        $rawToken = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $rawToken);

        $this->insert([
            'user_id'    => $userId,
            'email_hash' => $emailHash,
            'token_hash' => $tokenHash,
            'expires_at' => date('Y-m-d H:i:s', time() + 1800), // 30 minutes
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return $rawToken;
    }

    /**
     * Validate a token and return the record if active and not expired.
     */
    public function validateToken(string $rawToken): ?array
    {
        if (empty($rawToken)) {
            return null;
        }

        $tokenHash = hash('sha256', $rawToken);
        $record = $this->where('token_hash', $tokenHash)->first();

        if (!$record) {
            return null;
        }

        if (strtotime($record['expires_at']) < time()) {
            $this->delete($record['id']);
            return null;
        }

        return $record;
    }

    /**
     * Delete token after successful password reset.
     */
    public function invalidateToken(string $rawToken): bool
    {
        $tokenHash = hash('sha256', $rawToken);
        return (bool) $this->where('token_hash', $tokenHash)->delete();
    }
}
