<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateIncidentImportDeduplication extends Migration
{
    public function up()
    {
        $this->dropIndexIfExists('incident_reports', 'incident_reports_user_id_row_hash');
        $this->dropIndexIfExists('incident_reports', 'user_id');
        $this->dropIndexIfExists('incident_reports', 'row_hash');
        $this->forge->addKey('row_hash', false, true);
    }

    public function down()
    {
        $this->dropIndexIfExists('incident_reports', 'incident_reports_row_hash');
        $this->dropIndexIfExists('incident_reports', 'row_hash');
        $this->forge->addKey(['user_id', 'row_hash'], false, true);
    }

    private function dropIndexIfExists(string $table, string $indexName): void
    {
        $result = $this->db->query(
            'SELECT 1 FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = ? AND index_name = ? LIMIT 1',
            [$table, $indexName]
        );

        if (!empty($result->getResultArray())) {
            $this->db->query(sprintf('ALTER TABLE `%s` DROP INDEX `%s`', $table, $indexName));
        }
    }
}
