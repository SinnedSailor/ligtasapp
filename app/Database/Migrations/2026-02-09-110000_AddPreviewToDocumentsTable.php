<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPreviewToDocumentsTable extends Migration
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

        $this->forge->addColumn('documents', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('documents', ['preview_path', 'preview_mime']);
    }
}
