<?php
require("config.inc.php");

$conn = new mysqli($host, $user, $password, $db_name);

if ($conn->connect_errno > 0) {
    die("Fehler im Verbindungsaufbau: " . $conn->connect_error);
}
$conn->set_charset("UTF8");
