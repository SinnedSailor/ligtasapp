<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRowHashToIncidentReportsTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('incident_reports', [
            'row_hash' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
                'after' => 'user_id',
            ],
        ]);

        $this->forge->addKey(['user_id', 'row_hash'], false, true);
    }

    public function down()
    {
        $this->forge->dropKey('incident_reports', 'incident_reports_user_id_row_hash');
        $this->forge->dropColumn('incident_reports', ['row_hash']);
    }
}
