<?php
$db=new mysqli('127.0.0.1','root','','db_iwas');
if ($db->connect_error) { echo "connect error: " . $db->connect_error . "\n"; exit(1); }
$res=$db->query('SELECT * FROM migrations ORDER BY version ASC');
if (!$res) { echo "Query failed: " . $db->error . "\n"; exit(1); }
while($r=$res->fetch_assoc()){
    echo json_encode($r) . "\n";
}
