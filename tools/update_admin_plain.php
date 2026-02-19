<?php
require __DIR__ . '/../vendor/autoload.php';
// Update user id 1 with plaintext so model encrypts values on save
$model = new \App\Models\UserModel();
$data = [
    'first_name' => 'Admin',
    'last_name' => 'User',
    'email' => 'admin@iwas.local',
    'username' => 'admin',
    'password' => 'Admin@123456',
];
$model->update(1, $data);
echo "Admin updated\n";
