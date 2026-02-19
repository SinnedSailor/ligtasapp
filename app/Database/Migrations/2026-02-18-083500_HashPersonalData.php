<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * @property \CodeIgniter\Database\Forge $forge
 * @property \CodeIgniter\Database\BaseConnection $db
 */
class HashPersonalData extends Migration
{
    public function up()
    {
        // Add hash columns
        $this->forge->addColumn('users', [
            'email_hash' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
                'after' => 'email',
            ],
        ]);

        // Add victim name hash to incident_reports if table exists
        $db = \Config\Database::connect();
        if ($db->tableExists('incident_reports')) {
            $this->forge->addColumn('incident_reports', [
                'name_of_victim_hash' => [
                    'type' => 'VARCHAR',
                    'constraint' => 64,
                    'null' => true,
                    'after' => 'name_of_victim',
                ],
            ]);
        }

        // Backfill existing records with deterministic HMAC-SHA256 hashes
        $key = env('encryption.key') ?: (getenv('encryption.key') ?: 'CHANGE_ME__SET_ENCRYPTION_KEY');

        // Users: set email_hash and replace PII columns with their hashes
        if ($db->tableExists('users')) {
            $users = $db->table('users')->get()->getResultArray();
            foreach ($users as $user) {
                $email = $user['email'] ?? '';
                $first = $user['first_name'] ?? '';
                $last  = $user['last_name'] ?? '';
                $contact = $user['contact_number'] ?? null;

                $emailHash = $email !== '' ? hash_hmac('sha256', mb_strtolower(trim($email)), $key) : null;
                $firstHash = $first !== '' ? hash_hmac('sha256', mb_strtolower(trim($first)), $key) : null;
                $lastHash  = $last !== '' ? hash_hmac('sha256', mb_strtolower(trim($last)), $key) : null;
                $contactHash = $contact !== null && $contact !== '' ? hash_hmac('sha256', trim($contact), $key) : null;

                $update = [
                    'email_hash' => $emailHash,
                    'first_name' => $firstHash,
                    'last_name'  => $lastHash,
                    'email'      => $emailHash,
                    'contact_number' => $contactHash,
                ];

                $db->table('users')->where('id', $user['id'])->update($update);
            }

            // add unique index on email_hash (if not already present)
            try {
                $db->query('ALTER TABLE `users` ADD UNIQUE (`email_hash`)');
            } catch (\Exception $e) {
                // ignore if index already exists
            }
        }

        // Incident reports: hash victim name and replace
        if ($db->tableExists('incident_reports')) {
            $rows = $db->table('incident_reports')->get()->getResultArray();
            foreach ($rows as $r) {
                $victim = $r['name_of_victim'] ?? '';
                $victimHash = $victim !== '' ? hash_hmac('sha256', mb_strtolower(trim($victim)), $key) : null;
                $db->table('incident_reports')->where('id', $r['id'])->update([
                    'name_of_victim_hash' => $victimHash,
                    'name_of_victim' => $victimHash,
                ]);
            }
        }
    }

    public function down()
    {
        // Note: the actual plaintext values cannot be recovered once hashed.
        // The migration down will only drop the added hash columns.
        if ($this->db->fieldExists('email_hash', 'users')) {
            $this->forge->dropColumn('users', ['email_hash']);
        }

        $db = \Config\Database::connect();
        if ($db->tableExists('incident_reports') && $this->db->fieldExists('name_of_victim_hash', 'incident_reports')) {
            $this->forge->dropColumn('incident_reports', ['name_of_victim_hash']);
        }
    }
}
