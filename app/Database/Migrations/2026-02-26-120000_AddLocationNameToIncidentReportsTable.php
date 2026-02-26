<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLocationNameToIncidentReportsTable extends Migration
{
    public function up()
    {
        $fields = [
            'location_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 160,
                'null'       => true,
                'default'    => null,
                'after'      => 'location_category',
            ],
        ];
        $this->forge->addColumn('incident_reports', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('incident_reports', 'location_name');
    }
}
