<?php
$host     = "localhost"; // Database Host
$user     = "amaralqd_amaravathijuniorcollege"; // Database Username
$password = "Amaravathi@@2017"; // Database's user Password
$database = "amaralqd_security"; // Database Name

$mysqli = new mysqli($host, $user, $password, $database);

// Checking Connection
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli->connect_error;
    exit();
}

$mysqli->set_charset("utf8mb4");

// Settings
include "config_settings.php";
?>