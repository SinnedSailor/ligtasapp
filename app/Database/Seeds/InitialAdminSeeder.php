<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InitialAdminSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'first_name' => 'Admin',
            'last_name' => 'User',
            'username' => 'admin',
            'email' => 'admin@iwas.local',
            'password' => 'Admin@123456',
            'province' => 'System',
            'municipality' => 'System',
            'is_admin' => 1,
            'role_id' => null,
        ];

        // Use the UserModel so callbacks (password/hashPII) run for seeded data
        $userModel = new \App\Models\UserModel();
        $data = $userModel->prepareForInsert($data);
        $userModel->insert($data);
    }
}
