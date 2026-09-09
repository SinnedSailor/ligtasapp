<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOtpsAndPasswordResetsTables extends Migration
{
    public function up()
    {
        // 1. user_otps table for Admin 2FA
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'otp_hash' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'type' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'admin_login_2fa',
            ],
            'attempts' => [
                'type'       => 'INT',
                'constraint' => 3,
                'default'    => 0,
            ],
            'expires_at' => [
                'type' => 'DATETIME',
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
        $this->forge->addKey('user_id');
        $this->forge->addKey(['user_id', 'type']);
        $this->forge->createTable('user_otps', true);

        // 2. password_resets table for Password Reset via Email
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'email_hash' => [
                'type'       => 'VARCHAR',
                'constraint' => 64,
            ],
            'token_hash' => [
                'type'       => 'VARCHAR',
                'constraint' => 64,
            ],
            'expires_at' => [
                'type' => 'DATETIME',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->addKey('token_hash');
        $this->forge->createTable('password_resets', true);
    }

    public function down()
    {
        $this->forge->dropTable('user_otps', true);
        $this->forge->dropTable('password_resets', true);
    }
}
