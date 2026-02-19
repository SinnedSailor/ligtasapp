<?php
$db=new mysqli('127.0.0.1','root','','db_iwas');
if ($db->connect_error) { echo "connect error: " . $db->connect_error . "\n"; exit(1); }
$res = $db->query('SHOW COLUMNS FROM users');
echo "COLUMNS:\n";
while($r=$res->fetch_assoc()){ echo $r['Field'] . "\t" . $r['Type'] . "\n"; }

$res = $db->query('SELECT id,username,email_hash,email_enc,is_admin,created_at FROM users');
echo "\nROWS:\n";
while($r=$res->fetch_assoc()){ echo implode(' | ', $r) . "\n"; }
