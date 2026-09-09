<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddOtpColumnsToUsersTable extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('OTP', 'users')) {
            $this->forge->addColumn('users', [
                'OTP' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 10,
                    'null'       => true,
                    'after'      => 'is_active',
                ],
            ]);
        }

        if (! $this->db->fieldExists('OTP_EXPIRED', 'users')) {
            $this->forge->addColumn('users', [
                'OTP_EXPIRED' => [
                    'type'  => 'DATETIME',
                    'null'  => true,
                    'after' => 'OTP',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('OTP', 'users')) {
            $this->forge->dropColumn('users', 'OTP');
        }
        if ($this->db->fieldExists('OTP_EXPIRED', 'users')) {
            $this->forge->dropColumn('users', 'OTP_EXPIRED');
        }
    }
}
