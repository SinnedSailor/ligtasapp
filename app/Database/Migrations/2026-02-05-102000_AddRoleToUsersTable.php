<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRoleToUsersTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'role_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'municipality',
            ],
            'is_admin' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'after' => 'role_id',
            ],
        ]);

        // Add foreign key constraint
        $this->forge->addForeignKey('role_id', 'roles', 'id', 'SET NULL', 'CASCADE', false);
    }

    public function down()
    {
        $this->forge->dropForeignKey('users', 'role_id');
        $this->forge->dropColumn('users', ['role_id', 'is_admin']);
    }
}
