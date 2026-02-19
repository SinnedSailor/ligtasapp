<?php
$db=new mysqli('127.0.0.1','root','','db_iwas');
if ($db->connect_error) { echo "connect error: " . $db->connect_error . "\n"; exit(1); }
$res = $db->query('SHOW COLUMNS FROM incident_reports');
echo "COLUMNS:\n";
while($r=$res->fetch_assoc()){ echo $r['Field'] . "\t" . $r['Type'] . "\n"; }

$res = $db->query('SHOW INDEX FROM incident_reports');
echo "\nINDEXES:\n";
while($r=$res->fetch_assoc()){ echo $r['Key_name'] . "\t" . $r['Column_name'] . "\n"; }

$res = $db->query('SHOW CREATE TABLE `incident_reports`');
$create = $res->fetch_assoc();
echo "\nCREATE TABLE:\n" . $create['Create Table'] . "\n";
