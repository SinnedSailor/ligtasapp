<?php

namespace App\Models;

use CodeIgniter\Model;

class UserOtpModel extends Model
{
    protected $table            = 'user_otps';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'user_id',
        'otp_hash',
        'type',
        'attempts',
        'expires_at',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Generate a new 6-digit OTP for the given user.
     * Deletes any previous OTPs for the same user and type.
     */
    public function generateOtp(int $userId, string $type = 'admin_login_2fa'): string
    {
        // Delete older OTPs for this user & type
        $this->where('user_id', $userId)
             ->where('type', $type)
             ->delete();

        $rawOtp = sprintf('%06d', random_int(100000, 999999));
        $hash = password_hash($rawOtp, PASSWORD_DEFAULT);

        $this->insert([
            'user_id'    => $userId,
            'otp_hash'   => $hash,
            'type'       => $type,
            'attempts'   => 0,
            'expires_at' => date('Y-m-d H:i:s', time() + 600), // 10 minutes
        ]);

        return $rawOtp;
    }

    /**
     * Verify the supplied OTP for a user.
     * Returns an array with ['valid' => bool, 'message' => string].
     */
    public function verifyOtp(int $userId, string $rawOtp, string $type = 'admin_login_2fa'): array
    {
        $record = $this->where('user_id', $userId)
                       ->where('type', $type)
                       ->orderBy('id', 'DESC')
                       ->first();

        if (!$record) {
            return ['valid' => false, 'message' => 'No active OTP found. Please request a new code.'];
        }

        if (strtotime($record['expires_at']) < time()) {
            $this->delete($record['id']);
            return ['valid' => false, 'message' => 'The verification code has expired. Please request a new one.'];
        }

        if ($record['attempts'] >= 5) {
            $this->delete($record['id']);
            return ['valid' => false, 'message' => 'Too many failed attempts. Please request a new verification code.'];
        }

        // Increment attempt counter
        $this->update($record['id'], ['attempts' => $record['attempts'] + 1]);

        if (password_verify($rawOtp, $record['otp_hash'])) {
            // Success - delete used OTP
            $this->delete($record['id']);
            return ['valid' => true, 'message' => 'Verification successful.'];
        }

        $remaining = 4 - $record['attempts'];
        $remainMsg = $remaining > 0 ? " ({$remaining} attempts remaining)" : "";
        return ['valid' => false, 'message' => 'Invalid verification code.' . $remainMsg];
    }

    /**
     * Check if a new OTP can be requested (60 second cooldown).
     */
    public function canResend(int $userId, string $type = 'admin_login_2fa'): array
    {
        $record = $this->where('user_id', $userId)
                       ->where('type', $type)
                       ->orderBy('id', 'DESC')
                       ->first();

        if (!$record || empty($record['created_at'])) {
            return ['can_resend' => true, 'wait_seconds' => 0];
        }

        $elapsed = time() - strtotime($record['created_at']);
        if ($elapsed < 60) {
            return ['can_resend' => false, 'wait_seconds' => 60 - $elapsed];
        }

        return ['can_resend' => true, 'wait_seconds' => 0];
    }
}
