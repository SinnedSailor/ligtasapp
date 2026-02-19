<?php
$db=new mysqli('127.0.0.1','root','','db_iwas');
if($db->connect_error){ echo "connect error: " . $db->connect_error . "\n"; exit(1); }
$res = $db->query('ALTER TABLE `incident_reports` DROP COLUMN `name_of_victim`');
if ($res === false) {
    echo "ERROR: " . $db->error . "\n";
} else {
    echo "Dropped column successfully\n";
}
