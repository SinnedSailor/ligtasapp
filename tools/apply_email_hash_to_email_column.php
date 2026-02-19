<?php
$db=new mysqli('127.0.0.1','root','','db_iwas');
if ($db->connect_error) { echo "connect error: " . $db->connect_error . "\n"; exit(1); }
// If `email_hash` column exists, copy into `email`; otherwise do nothing.
$res = $db->query("SELECT COUNT(*) AS c FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='db_iwas' AND TABLE_NAME='users' AND COLUMN_NAME='email_hash'");
if ($res) {
    $r = $res->fetch_assoc();
    if ((int) $r['c'] > 0) {
        $res2 = $db->query("UPDATE users SET email = email_hash WHERE email_hash IS NOT NULL");
        if ($res2 === false) { echo "Update failed: " . $db->error . "\n"; exit(1); }
        echo "Updated rows: " . $db->affected_rows . "\n";
    } else {
        echo "No email_hash column found — nothing to do.\n";
    }
} else {
    echo "Failed to check columns: " . $db->error . "\n";
}
