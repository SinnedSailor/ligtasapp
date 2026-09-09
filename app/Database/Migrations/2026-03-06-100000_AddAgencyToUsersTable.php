<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAgencyToUsersTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'agency' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'municipality',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'agency');
    }
}
