<?php
require __DIR__ . "/../lib/db.php";

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["id"])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing id"]);
    exit;
}

$id = intval($data["id"]);

$stmt = $conn->prepare("DELETE FROM fingerprints WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

echo json_encode([
    "status" => "ok",
    "deleted_id" => $id
]);
