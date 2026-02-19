<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Convert existing stored user names to Title Case (first letter capitalized).
 * This migration is idempotent and safe to run after previous casing migrations.
 */
class ConvertUserNamesToTitleCase extends Migration
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

                $process = function ($val) use ($encrypter) {
                    if (empty($val)) return null;

                    $decoded = base64_decode($val, true);
                    if ($decoded !== false) {
                        try {
                            $plain = $encrypter->decrypt($decoded);
                        } catch (\Throwable $e) {
                            $plain = null;
                        }
                    } else {
                        $plain = $val;
                    }

                    if ($plain === null || $plain === '') return null;

                    $title = mb_convert_case(trim((string) $plain), MB_CASE_TITLE, 'UTF-8');
                    if ($title === trim((string) $plain)) {
                        return null; // already Title Case
                    }

                    return base64_encode($encrypter->encrypt($title));
                };

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
            log_message('error', '[ConvertUserNamesToTitleCase] migration error: ' . $e->getMessage());
        }
    }

    public function down()
    {
        // Irreversible normalization
    }
}
