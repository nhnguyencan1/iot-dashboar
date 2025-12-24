<?php
require __DIR__ . "/../lib/db.php";

$sql = "
    SELECT 
        temperature, humidity,
        DATE_FORMAT(created_at, '%H:%i:%s') AS time
    FROM dht_logs
    ORDER BY created_at DESC
    LIMIT 10
";

$result = $conn->query($sql);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

header('Content-Type: application/json');
echo json_encode(array_reverse($data));
