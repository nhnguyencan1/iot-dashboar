<?php
function save_fingerprint_log(mysqli $conn, int $finger_id, string $event = 'matched') {

    echo ">>> save_fingerprint_log() START\n";

    // 1. Lấy tên vân tay
    $stmt = $conn->prepare("SELECT name FROM fingerprints WHERE id = ?");
    if (!$stmt) {
        echo "❌ Prepare SELECT failed: ".$conn->error."\n";
        return;
    }

    $stmt->bind_param("i", $finger_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    $stmt->close();

    $name = $row ? $row['name'] : 'Unknown';

    // 2. Insert log
    $stmt = $conn->prepare("
        INSERT INTO fingerprint_logs (finger_id, finger_name, event)
        VALUES (?, ?, ?)
    ");
    if (!$stmt) {
        echo "❌ Prepare INSERT failed: ".$conn->error."\n";
        return;
    }

    $stmt->bind_param("iss", $finger_id, $name, $event);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "✅ INSERT fingerprint_log OK (ID=$finger_id, name=$name)\n";
    } else {
        echo "❌ INSERT failed\n";
    }

    $stmt->close();
}
