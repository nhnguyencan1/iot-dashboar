<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . "/../lib/db.php";

// xóa toàn bộ danh sách đăng ký
$conn->query("DELETE FROM fingerprints");

echo json_encode(["status" => "ok", "deleted" => "all"]);
