<?php
$db=new mysqli('127.0.0.1','root','','db_iwas');
if($db->connect_error){ echo "connect error\n"; exit(1); }
$r = $db->query('ALTER TABLE `incident_reports` DROP INDEX `name_of_victim_municipality_province_year_of_incident`');
if ($r === false) {
    echo "DROP INDEX ERROR: " . $db->error . "\n";
} else {
    echo "Dropped index successfully\n";
}
