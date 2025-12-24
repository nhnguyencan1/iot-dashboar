<?php
require __DIR__ . "/../lib/db.php";

$sql = "
    SELECT finger_id, finger_name, event, created_at
    FROM fingerprint_logs
    ORDER BY created_at DESC
    LIMIT 50
";

$res = $conn->query($sql);

$data = [];
while ($row = $res->fetch_assoc()) {
    $data[] = $row;
}

header("Content-Type: application/json");
echo json_encode($data);
