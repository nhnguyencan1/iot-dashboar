<?php
require __DIR__ . "/../lib/db.php";

// Xóa toàn bộ lịch sử quét
$sql = "TRUNCATE TABLE fingerprint_logs";

if ($conn->query($sql)) {
    echo json_encode([
        "status" => "ok",
        "message" => "Đã xóa toàn bộ lịch sử quét"
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Không thể xóa lịch sử"
    ]);
}
