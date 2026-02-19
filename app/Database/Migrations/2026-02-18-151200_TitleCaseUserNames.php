<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * One-time migration: decrypt existing user name PII, convert to Title Case,
 * and re-encrypt so names display with capitalized words going forward.
 *
 * NOTE: this is irreversible because original case is not stored anywhere.
 */
class TitleCaseUserNames extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        $encrypter = \Config\Services::encrypter();

        try {
            $users = $db->table('users')
                ->select('id, first_name_enc, last_name_enc, first_name, last_name')
                ->get()
                ->getResultArray();

            foreach ($users as $u) {
                $update = [];

                // Helper to decrypt (if base64 ciphertext) and title-case then re-encrypt
                $process = function ($val) use ($encrypter) {
                    if (empty($val)) return null;

                    // If value looks like base64 ciphertext, try to decode/decrypt
                    $decoded = base64_decode($val, true);
                    if ($decoded !== false) {
                        try {
                            $plain = $encrypter->decrypt($decoded);
                        } catch (\Throwable $e) {
                            $plain = null;
                        }
                    } else {
                        // treat as plaintext fallback
                        $plain = $val;
                    }

                    if (empty($plain)) return null;

                    $title = mb_convert_case(trim((string) $plain), MB_CASE_TITLE, 'UTF-8');
                    if ($title === trim((string) $plain)) {
                        return null; // no change needed
                    }

                    return base64_encode($encrypter->encrypt($title));
                };

                // Prefer encrypted columns when present, otherwise use plaintext column
                if (!empty($u['first_name_enc']) || !empty($u['first_name'])) {
                    $src = $u['first_name_enc'] ?: $u['first_name'];
                    $new = $process($src);
                    if ($new !== null) {
                        $update['first_name_enc'] = $new;
                    }
                }

                if (!empty($u['last_name_enc']) || !empty($u['last_name'])) {
                    $src = $u['last_name_enc'] ?: $u['last_name'];
                    $new = $process($src);
                    if ($new !== null) {
                        $update['last_name_enc'] = $new;
                    }
                }

                if (!empty($update)) {
                    $db->table('users')->where('id', $u['id'])->update($update);
                }
            }
        } catch (\Throwable $e) {
            // Do not halt migration for individual failures
            log_message('error', '[TitleCaseUserNames] migration error: ' . $e->getMessage());
        }
    }

    public function down()
    {
        // irreversible: do nothing
    }
}
