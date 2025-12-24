<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

$host = "127.0.0.1";
$user = "root";
$pass = "";
$dbname = "iot_db";
$port = 3307;

$conn = mysqli_connect($host, $user, $pass, $dbname, $port);

if (!$conn) {
    http_response_code(500);
    die("Database connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8");
?>
