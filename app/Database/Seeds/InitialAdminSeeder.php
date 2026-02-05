<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InitialAdminSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'first_name' => 'Admin',
                'last_name' => 'User',
                'username' => 'admin',
                'email' => 'admin@iwas.local',
                'password' => password_hash('Admin@123456', PASSWORD_DEFAULT),
                'province' => 'System',
                'municipality' => 'System',
                'is_admin' => 1,
                'role_id' => null,
                'created_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('users')->insertBatch($data);
    }
}
