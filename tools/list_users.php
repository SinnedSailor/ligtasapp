<?php
$db=new mysqli('127.0.0.1','root','','db_iwas');
if ($db->connect_error) { echo "connect error: " . $db->connect_error . "\n"; exit(1); }
$res = $db->query('SELECT id,username,email,is_admin,created_at FROM users');
while($r=$res->fetch_assoc()){ echo implode(' | ', $r) . "\n"; }
