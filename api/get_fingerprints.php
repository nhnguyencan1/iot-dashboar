<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require __DIR__ . "/../lib/db.php";

$sql = "SELECT id, name FROM fingerprints ORDER BY id";
$res = $conn->query($sql);

if (!$res) {
    http_response_code(500);
    echo json_encode([
        "error" => "Query failed",
        "msg" => $conn->error
    ]);
    exit;
}

$data = [];
while ($row = $res->fetch_assoc()) {
    $data[] = $row;
}

header("Content-Type: application/json; charset=utf-8");
echo json_encode($data, JSON_UNESCAPED_UNICODE);
