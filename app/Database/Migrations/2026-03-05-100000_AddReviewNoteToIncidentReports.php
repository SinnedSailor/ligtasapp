<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddReviewNoteToIncidentReports extends Migration
{
    public function up()
    {
        // add a nullable text column to store review notes when an incident is rejected
        $this->forge->addColumn('incident_reports', [
            'review_note' => [
                'type'       => 'TEXT',
                'null'       => true,
                'after'      => 'review_status',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('incident_reports', ['review_note']);
    }
}
