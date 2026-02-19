<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveEmailHash extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        // Add UNIQUE index on `email` (if not present) so uniqueness remains enforced
        try {
            $db->query('ALTER TABLE `users` ADD UNIQUE (`email`)');
        } catch (\Exception $e) {
            // ignore if index already exists
        }

        // Drop the `email_hash` column if it exists
        if ($this->db->fieldExists('email_hash', 'users')) {
            $this->forge->dropColumn('users', ['email_hash']);
        }
    }

    public function down()
    {
        // Recreate `email_hash` column and backfill from `email` if necessary
        if (! $this->db->fieldExists('email_hash', 'users')) {
            $this->forge->addColumn('users', [
                'email_hash' => [
                    'type' => 'VARCHAR',
                    'constraint' => 64,
                    'null' => true,
                    'after' => 'email',
                ],
            ]);

            $db = \Config\Database::connect();
            // Backfill from `email` column when it already contains the deterministic hash
            try {
                $rows = $db->table('users')->select('id,email')->get()->getResultArray();
                foreach ($rows as $r) {
                    $db->table('users')->where('id', $r['id'])->update(['email_hash' => $r['email']]);
                }
            } catch (\Throwable $e) {
                // ignore
            }

            try {
                $db->query('ALTER TABLE `users` ADD UNIQUE (`email_hash`)');
            } catch (\Exception $e) {
                // ignore
            }
        }
    }
}
