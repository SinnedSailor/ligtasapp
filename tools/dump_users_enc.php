<?php
$db=new mysqli('127.0.0.1','root','','db_iwas');
if ($db->connect_error) { die($db->connect_error); }
$res=$db->query('SELECT id, first_name_enc, last_name_enc, email_enc, contact_number_enc FROM users');
while($r=$res->fetch_assoc()){ print_r($r); }
