<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateIncidentReportAttachmentsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'incident_n' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'file_kind' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],
            'original_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'stored_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'stored_path' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'mime_type' => [
                'type' => 'VARCHAR',
                'constraint' => 120,
                'null' => true,
            ],
            'size_bytes' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => 0,
            ],
            'uploaded_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('incident_n');
        $this->forge->createTable('incident_report_attachments');
    }

    public function down()
    {
        $this->forge->dropTable('incident_report_attachments');
    }
}
