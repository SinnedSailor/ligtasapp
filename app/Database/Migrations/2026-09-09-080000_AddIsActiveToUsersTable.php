<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIsActiveToUsersTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'null'       => false,
                'after'      => 'is_admin',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'is_active');
    }
}
