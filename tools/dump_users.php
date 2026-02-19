<?php
$db = new mysqli('127.0.0.1','root','','db_iwas');
if ($db->connect_error) { die($db->connect_error); }
$res = $db->query('SELECT * FROM users');
while ($r = $res->fetch_assoc()) {
    print_r($r);
}
