<?php
$db = new mysqli('127.0.0.1','root','','db_iwas');
if ($db->connect_error) {
    echo json_encode(['error' => $db->connect_error]);
    exit(1);
}
$res = $db->query("SELECT * FROM users WHERE username='admin' OR id=1 LIMIT 1");
$row = $res ? $res->fetch_assoc() : null;
file_put_contents(__DIR__ . '/admin-backup-' . date('Ymd-His') . '.json', json_encode($row, JSON_PRETTY_PRINT));
echo json_encode($row, JSON_PRETTY_PRINT);
