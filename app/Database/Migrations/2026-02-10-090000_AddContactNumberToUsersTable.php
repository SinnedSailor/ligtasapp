<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddContactNumberToUsersTable extends Migration
{
    public function up()
    {
        $fields = [
            'contact_number' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'after' => 'email',
            ],
        ];

        $this->forge->addColumn('users', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('users', ['contact_number']);
    }
}
