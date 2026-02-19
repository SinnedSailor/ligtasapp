<?php
// One-off script to truncate users and incident report tables.
$db = new mysqli('127.0.0.1', 'root', '', 'db_iwas');
if ($db->connect_error) {
    echo "DB connect error: " . $db->connect_error . "\n";
    exit(1);
}
$queries = [
    'SET FOREIGN_KEY_CHECKS=0',
    'TRUNCATE TABLE incident_report_attachments',
    'TRUNCATE TABLE incident_reports',
    'TRUNCATE TABLE users',
    'SET FOREIGN_KEY_CHECKS=1',
];
foreach ($queries as $q) {
    if (! $db->query($q)) {
        echo "Query failed: ($q) - " . $db->error . "\n";
        exit(1);
    }
}
echo "Truncate completed.\n";
