<?php
$db=new mysqli('127.0.0.1','root','','db_iwas');
if ($db->connect_error) { echo "connect error: " . $db->connect_error . "\n"; exit(1); }
$res=$db->query('SELECT n, name_of_victim_enc, name_of_victim_hash, created_at FROM incident_reports ORDER BY n ASC');
while($r=$res->fetch_assoc()){
    echo json_encode($r) . "\n";
}
