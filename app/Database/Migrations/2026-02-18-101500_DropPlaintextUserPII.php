<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * @property \CodeIgniter\Database\Forge $forge
 * @property \CodeIgniter\Database\BaseConnection $db
 */
class DropPlaintextUserPII extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        // Ensure `email_hash` exists and backfill from `email` if necessary
        if (! $this->db->fieldExists('email_hash', 'users')) {
            $this->forge->addColumn('users', [
                'email_hash' => [
                    'type' => 'VARCHAR',
                    'constraint' => 64,
                    'null' => true,
                    'after' => 'email',
                ],
            ]);
        }

        // Backfill email_hash from `email` (if `email` currently contains plaintext or hash)
        try {
            $rows = $db->table('users')->select('id,email')->get()->getResultArray();
            $key = env('encryption.key') ?: (getenv('encryption.key') ?: 'CHANGE_ME__SET_ENCRYPTION_KEY');
            foreach ($rows as $r) {
                $email = $r['email'] ?? '';
                if ($email === null || $email === '') continue;
                // If `email` already looks like a 64-char hash, copy it; otherwise compute HMAC-SHA256
                if (preg_match('/^[0-9a-f]{64}$/i', (string) $email)) {
                    $db->table('users')->where('id', $r['id'])->update(['email_hash' => $email]);
                } else {
                    $emailHash = hash_hmac('sha256', mb_strtolower(trim($email)), $key);
                    $db->table('users')->where('id', $r['id'])->update(['email_hash' => $emailHash]);
                }
            }
        } catch (\Throwable $e) {
            // ignore
        }

        // Backfill reversible encrypted columns from plaintext where possible
        try {
            $encrypter = \Config\Services::encrypter();
            $users = $db->table('users')->select('id, first_name, last_name, contact_number, first_name_enc, last_name_enc, contact_number_enc')->get()->getResultArray();
            foreach ($users as $u) {
                $update = [];
                if (empty($u['first_name_enc']) && !empty($u['first_name'])) {
                    $update['first_name_enc'] = base64_encode($encrypter->encrypt(mb_strtolower(trim($u['first_name']))));
                }
                if (empty($u['last_name_enc']) && !empty($u['last_name'])) {
                    $update['last_name_enc'] = base64_encode($encrypter->encrypt(mb_strtolower(trim($u['last_name']))));
                }
                if (empty($u['contact_number_enc']) && !empty($u['contact_number'])) {
                    $update['contact_number_enc'] = base64_encode($encrypter->encrypt(trim($u['contact_number'])));
                }
                if (!empty($update)) {
                    $db->table('users')->where('id', $u['id'])->update($update);
                }
            }
        } catch (\Throwable $e) {
            // ignore
        }

        // Add unique index on email_hash
        try {
            $db->query('ALTER TABLE `users` ADD UNIQUE (`email_hash`)');
        } catch (\Throwable $e) {
            // ignore if exists
        }

        // Finally drop plaintext columns if present
        $drop = [];
        if ($this->db->fieldExists('first_name', 'users')) {
            $drop[] = 'first_name';
        }
        if ($this->db->fieldExists('last_name', 'users')) {
            $drop[] = 'last_name';
        }
        if ($this->db->fieldExists('contact_number', 'users')) {
            $drop[] = 'contact_number';
        }
        if ($this->db->fieldExists('email', 'users')) {
            $drop[] = 'email';
        }

        if (! empty($drop)) {
            $this->forge->dropColumn('users', $drop);
        }
    }

    public function down()
    {
        // Recreate plaintext columns (no data restoration)
        $cols = [];
        if (! $this->db->fieldExists('first_name', 'users')) {
            $cols['first_name'] = [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'id',
            ];
        }
        if (! $this->db->fieldExists('last_name', 'users')) {
            $cols['last_name'] = [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'first_name',
            ];
        }
        if (! $this->db->fieldExists('email', 'users')) {
            $cols['email'] = [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'username',
            ];
        }
        if (! $this->db->fieldExists('contact_number', 'users')) {
            $cols['contact_number'] = [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'municipality',
            ];
        }

        if (! empty($cols)) {
            $this->forge->addColumn('users', $cols);
        }
    }
}
