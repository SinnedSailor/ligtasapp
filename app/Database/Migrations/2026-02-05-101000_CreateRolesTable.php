<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRolesTable extends Migration
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
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'unique' => true,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
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
        $this->forge->createTable('roles');

        // Insert default roles
        $this->db->table('roles')->insertBatch([
            [
                'name' => 'ADMIN',
                'description' => 'System Administrator with full access',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'FOCAL',
                'description' => 'Focal point user',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'LGU',
                'description' => 'Local Government Unit user',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'PROVINCIAL',
                'description' => 'Provincial user',
                'created_at' => date('Y-m-d H:i:s'),
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('roles');
    }
}
