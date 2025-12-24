<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . "/../lib/db.php";

header("Content-Type: application/json");

$sql = "TRUNCATE TABLE system_logs"; 

if ($conn->query($sql)) {
    echo json_encode([
        "status" => "ok"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "error" => $conn->error
    ]);
}
