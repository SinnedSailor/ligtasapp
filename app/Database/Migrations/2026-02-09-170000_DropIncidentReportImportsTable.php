<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DropIncidentReportImportsTable extends Migration
{
    public function up()
    {
        $this->forge->dropTable('incident_report_imports', true);
    }

    public function down()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'file_hash' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
            ],
            'rows_count' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('file_hash', false, true);
        $this->forge->createTable('incident_report_imports');
    }
}
