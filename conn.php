<?php
$mysqli = new mysqli('localhost', 'root', '', 'hotel_db');
if ($mysqli->connect_error) { die('DB connect failed: '.$mysqli->connect_error); }
$mysqli->set_charset('utf8mb4');
