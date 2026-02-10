<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPreviewToIncidentAttachments extends Migration
{
    public function up()
    {
        $fields = [
            'preview_path' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'stored_path',
            ],
            'preview_mime' => [
                'type' => 'VARCHAR',
                'constraint' => 120,
                'null' => true,
                'after' => 'preview_path',
            ],
        ];

        $this->forge->addColumn('incident_report_attachments', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('incident_report_attachments', ['preview_path', 'preview_mime']);
    }
}
