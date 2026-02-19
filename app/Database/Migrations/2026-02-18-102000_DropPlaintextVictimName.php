<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DropPlaintextVictimName extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        // Ensure `name_of_victim_enc` exists (place after `name_of_victim` when present,
        // otherwise add it after `municipality` to avoid referencing a missing column)
        if (! $this->db->fieldExists('name_of_victim_enc', 'incident_reports')) {
            $after = $this->db->fieldExists('name_of_victim', 'incident_reports') ? 'name_of_victim' : 'municipality';
            $this->forge->addColumn('incident_reports', [
                'name_of_victim_enc' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'after' => $after,
                ],
            ]);
        }

        // Backfill encrypted or hashed columns where possible
        try {
            $rows = $db->table('incident_reports')->select('id, name_of_victim, name_of_victim_enc, name_of_victim_hash')->get()->getResultArray();
            $encrypter = \Config\Services::encrypter();
            $key = env('encryption.key') ?: (getenv('encryption.key') ?: 'CHANGE_ME__SET_ENCRYPTION_KEY');

            foreach ($rows as $r) {
                $update = [];
                $victim = $r['name_of_victim'] ?? null;

                if ($victim === null || $victim === '') {
                    // nothing to do
                    continue;
                }

                // If column already contains a 64-char hex hash, copy into name_of_victim_hash (if missing)
                if (preg_match('/^[0-9a-f]{64}$/i', (string) $victim)) {
                    if (empty($r['name_of_victim_hash'])) {
                        $update['name_of_victim_hash'] = $victim;
                    }
                } else {
                    // Treat as plaintext: backfill `name_of_victim_enc` and `name_of_victim_hash` when not present
                    if (empty($r['name_of_victim_enc'])) {
                        $update['name_of_victim_enc'] = base64_encode($encrypter->encrypt(mb_strtolower(trim($victim))));
                    }
                    if (empty($r['name_of_victim_hash'])) {
                        $update['name_of_victim_hash'] = hash_hmac('sha256', mb_strtolower(trim($victim)), $key);
                    }
                }

                if (! empty($update)) {
                    $db->table('incident_reports')->where('id', $r['id'])->update($update);
                }
            }
        } catch (\Throwable $e) {
            // ignore failures to backfill
        }

        // Drop any indexes that reference `name_of_victim` first, then drop the column
        try {
            $indexes = $db->query("SHOW INDEX FROM `incident_reports`")->fetch_all(MYSQLI_ASSOC);
            foreach ($indexes as $idx) {
                if ($idx['Key_name'] === 'name_of_victim_municipality_province_year_of_incident') {
                    $db->query('ALTER TABLE `incident_reports` DROP INDEX `name_of_victim_municipality_province_year_of_incident`');
                    break;
                }
            }
        } catch (\Throwable $e) {
            // ignore
        }

        if ($this->db->fieldExists('name_of_victim', 'incident_reports')) {
            $this->forge->dropColumn('incident_reports', ['name_of_victim']);
        }
    }

    public function down()
    {
        // Recreate the plaintext column (no plaintext restoration)
        if (! $this->db->fieldExists('name_of_victim', 'incident_reports')) {
            $this->forge->addColumn('incident_reports', [
                'name_of_victim' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => true,
                    'after' => 'municipality',
                ],
            ]);
        }
    }
}
