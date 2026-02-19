<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * One-time migration: decrypt existing user name PII, convert to UPPERCASE,
 * and re-encrypt so names are stored and shown in ALL CAPS going forward.
 *
 * NOTE: irreversible.
 */
class UppercaseUserNames extends Migration
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

                    if (empty($plain)) return null;

                    $upper = mb_strtoupper(trim((string) $plain));
                    if ($upper === trim((string) $plain)) {
                        return null; // no change
                    }

                    return base64_encode($encrypter->encrypt($upper));
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
            log_message('error', '[UppercaseUserNames] migration error: ' . $e->getMessage());
        }
    }

    public function down()
    {
        // irreversible
    }
}
