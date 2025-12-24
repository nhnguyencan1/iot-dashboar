<?php
require "../lib/db.php";

$limit = $_GET['limit'] ?? 100;

$sql = "SELECT * FROM system_logs
        ORDER BY created_at DESC
        LIMIT ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $limit);
$stmt->execute();

$res = $stmt->get_result();
$data = [];

while ($row = $res->fetch_assoc()) {
    $data[] = $row;
}

header("Content-Type: application/json");
echo json_encode($data);
