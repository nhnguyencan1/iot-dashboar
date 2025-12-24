<?php
require __DIR__ . "/../lib/db.php";

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["temp"]) || !isset($data["humi"])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing data"]);
    exit;
}

$temp = floatval($data["temp"]);
$humi = floatval($data["humi"]);

$stmt = $conn->prepare(
    "INSERT INTO dht_logs (temperature, humidity) VALUES (?, ?)"
);
$stmt->bind_param("dd", $temp, $humi);
$stmt->execute();

echo json_encode(["status" => "ok"]);
