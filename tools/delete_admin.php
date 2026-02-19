<?php
$db = new mysqli('127.0.0.1','root','','db_iwas');
if ($db->connect_error) {
    echo "DB connect error: " . $db->connect_error . "\n";
    exit(1);
}
$res = $db->query("DELETE FROM users WHERE username='admin'");
if ($res) {
    echo "Deleted admin user (username 'admin'). Rows affected: " . $db->affected_rows . "\n";
} else {
    echo "Delete failed: " . $db->error . "\n";
}
