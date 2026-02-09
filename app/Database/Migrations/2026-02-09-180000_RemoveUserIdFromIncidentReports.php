<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveUserIdFromIncidentReports extends Migration
{
    public function up()
    {
        $this->dropIndexIfExists('incident_reports', 'user_id');
        $this->forge->dropColumn('incident_reports', 'user_id');
    }

    public function down()
    {
        $this->forge->addColumn('incident_reports', [
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
        ]);
        $this->forge->addKey('user_id');
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
