<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddReviewFieldsToIncidentReports extends Migration
{
    public function up()
    {
        $fields = [
            'review_status' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'after' => 'remarks',
            ],
            'reviewed_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'review_status',
            ],
            'reviewed_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'reviewed_by',
            ],
        ];

        $this->forge->addColumn('incident_reports', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('incident_reports', ['review_status', 'reviewed_by', 'reviewed_at']);
    }
}
