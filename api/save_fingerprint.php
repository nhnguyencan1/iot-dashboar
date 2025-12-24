<?php
require __DIR__ . "/../lib/db.php";

$data = json_decode(file_get_contents("php://input"), true);

$id = intval($data["id"]);
$name = $data["name"];

$sql = "REPLACE INTO fingerprints (id, name) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $id, $name);
$stmt->execute();

echo "OK";
