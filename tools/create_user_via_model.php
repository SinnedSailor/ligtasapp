<?php
require __DIR__ . '/../vendor/autoload.php';

$dbConfig = require __DIR__ . '/../app/Config/Database.php';
// Use CodeIgniter Model directly
$userModel = new \App\Models\UserModel();

$data = [
    'first_name' => 'CLI',
    'last_name' => 'User',
    'username' => 'cli_user_' . time(),
    'email' => 'cli_user_' . time() . '@example.com',
    'password' => 'Cli@123456',
    'province' => 'Ilocos Norte',
    'municipality' => 'Batac City'
];

$data = $userModel->prepareForInsert($data);
$insertId = $userModel->insert($data);
if ($insertId === false) {
    echo "Insert failed: ";
    print_r($userModel->errors());
    exit(1);
}

echo "Inserted user ID: $insertId\n";
$res = $userModel->find($insertId);
print_r([
    'id' => $res['id'] ?? null,
    'username' => $res['username'] ?? null,
    'first_name_enc' => $res['first_name_enc'] ?? null,
    'last_name_enc' => $res['last_name_enc'] ?? null,
    'email_hash' => $res['email_hash'] ?? null,
    'email_enc' => $res['email_enc'] ?? null,
]);
