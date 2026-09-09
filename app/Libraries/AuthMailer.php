<?php

namespace App\Libraries;

use Config\Services;

class AuthMailer
{
    /**
     * Send Admin 2FA verification OTP.
     */
    public function sendAdminOtp(string $recipientEmail, string $recipientName, string $rawOtp): array
    {
        $subject = 'IWAS Admin Security Code: ' . $rawOtp;
        $message = "
        <div style='font-family: Arial, sans-serif; max-width: 560px; margin: 0 auto; padding: 24px; border: 1px solid #e2e8f0; border-radius: 12px; background-color: #ffffff;'>
            <div style='text-align: center; margin-bottom: 24px;'>
                <h2 style='color: #0f172a; margin: 0;'>IWAS Admin Security</h2>
                <p style='color: #64748b; font-size: 14px; margin-top: 4px;'>Two-Factor Authentication Verification</p>
            </div>
            <p style='color: #334155; font-size: 15px;'>Hello <strong>" . esc($recipientName) . "</strong>,</p>
            <p style='color: #334155; font-size: 15px;'>Your 6-digit authentication code for logging into the IWAS Administrator Panel is:</p>
            <div style='text-align: center; margin: 28px 0;'>
                <span style='display: inline-block; font-size: 32px; font-weight: 800; letter-spacing: 8px; color: #4338ca; background: #e0e7ff; padding: 12px 28px; border-radius: 10px; border: 1px dashed #6366f1;'>" . esc($rawOtp) . "</span>
            </div>
            <p style='color: #64748b; font-size: 13px; text-align: center;'>This code expires in <strong>10 minutes</strong>. If you did not attempt to log in, please secure your account immediately.</p>
            <hr style='border: none; border-top: 1px solid #f1f5f9; margin: 24px 0;'>
            <p style='color: #94a3b8; font-size: 12px; text-align: center;'>Integrated Women Abuse System (IWAS) &copy; " . date('Y') . "</p>
        </div>
        ";

        $emailSent = $this->dispatchEmail($recipientEmail, $subject, $message);

        // For local development convenience if SMTP is not set up
        if (ENVIRONMENT === 'development') {
            session()->setFlashdata('dev_otp_preview', $rawOtp);
            log_message('info', "[AUTH 2FA] OTP for {$recipientEmail}: {$rawOtp}");
        }

        return [
            'success'     => $emailSent || ENVIRONMENT === 'development',
            'email_sent'  => $emailSent,
            'otp'         => $rawOtp,
        ];
    }

    /**
     * Send Password Reset Link.
     */
    public function sendPasswordReset(string $recipientEmail, string $recipientName, string $rawToken): array
    {
        $resetUrl = base_url('reset-password/' . $rawToken);
        $subject  = 'IWAS - Reset Your Password';
        $message  = "
        <div style='font-family: Arial, sans-serif; max-width: 560px; margin: 0 auto; padding: 24px; border: 1px solid #e2e8f0; border-radius: 12px; background-color: #ffffff;'>
            <div style='text-align: center; margin-bottom: 24px;'>
                <h2 style='color: #0f172a; margin: 0;'>Password Reset Request</h2>
                <p style='color: #64748b; font-size: 14px; margin-top: 4px;'>Integrated Women Abuse System</p>
            </div>
            <p style='color: #334155; font-size: 15px;'>Hello <strong>" . esc($recipientName) . "</strong>,</p>
            <p style='color: #334155; font-size: 15px;'>We received a request to reset your password for your IWAS account. Click the button below to proceed:</p>
            <div style='text-align: center; margin: 30px 0;'>
                <a href='" . esc($resetUrl) . "' style='background-color: #4338ca; color: #ffffff; padding: 12px 28px; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 15px; display: inline-block; box-shadow: 0 4px 6px -1px rgba(67, 56, 202, 0.3);'>Reset Password</a>
            </div>
            <p style='color: #64748b; font-size: 13px;'>Or copy and paste this link into your browser:<br><a href='" . esc($resetUrl) . "' style='color: #4f46e5; word-break: break-all;'>" . esc($resetUrl) . "</a></p>
            <p style='color: #64748b; font-size: 13px;'>This password reset link is valid for <strong>30 minutes</strong>. If you did not request this, you can safely ignore this email.</p>
            <hr style='border: none; border-top: 1px solid #f1f5f9; margin: 24px 0;'>
            <p style='color: #94a3b8; font-size: 12px; text-align: center;'>Integrated Women Abuse System (IWAS) &copy; " . date('Y') . "</p>
        </div>
        ";

        $emailSent = $this->dispatchEmail($recipientEmail, $subject, $message);

        if (ENVIRONMENT === 'development') {
            session()->setFlashdata('dev_reset_preview', $resetUrl);
            log_message('info', "[PASSWORD RESET] Reset URL for {$recipientEmail}: {$resetUrl}");
        }

        return [
            'success'    => $emailSent || ENVIRONMENT === 'development',
            'email_sent' => $emailSent,
            'reset_url'  => $resetUrl,
        ];
    }

    /**
     * Internal email dispatch via CodeIgniter Email service.
     */
    protected function dispatchEmail(string $to, string $subject, string $htmlBody): bool
    {
        try {
            $email = Services::email();
            $config = config('Email');

            // Default sender if not set in Config\Email
            $fromEmail = !empty($config->fromEmail) ? $config->fromEmail : 'no-reply@iwas-system.local';
            $fromName  = !empty($config->fromName) ? $config->fromName : 'IWAS Security Team';

            $email->setFrom($fromEmail, $fromName);
            $email->setTo($to);
            $email->setSubject($subject);
            $email->setMessage($htmlBody);
            $email->setMailType('html');

            return (bool) $email->send(false);
        } catch (\Throwable $e) {
            log_message('error', '[AuthMailer] Email send failed: ' . $e->getMessage());
            return false;
        }
    }
}
