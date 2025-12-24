<?php
/**
 * Lưu event/log vào database
 * 
 * @param mysqli $conn - Database connection
 * @param string $topic - MQTT topic
 * @param string $message - Message content
 * @param string $source - Source của event (default: esp32)
 * @return bool - true nếu lưu thành công, false nếu lỗi
 */
function save_event($conn, $topic, $message, $source = "esp32") {
    echo ">>> SAVE_EVENT CALLED <<<\n";
    
    // Debug connection
    if (!$conn) {
        echo "❌ ERROR: \$conn is NULL\n";
        return false;
    }
    
    if ($conn->connect_error) {
        echo "❌ ERROR: DB connection error - " . $conn->connect_error . "\n";
        return false;
    }

    // Xác định level dựa trên nội dung message
    $level = "INFO";

    if (stripos($message, "fire") !== false) {
        $level = "FIRE";
    } elseif (stripos($message, "error") !== false) {
        $level = "ERROR";
    } elseif (stripos($message, "warning") !== false) {
        $level = "WARNING";
    }

    // Prepare statement
    $stmt = $conn->prepare(
        "INSERT INTO system_logs (source, topic, level, message)
         VALUES (?, ?, ?, ?)"
    );

    if (!$stmt) {
        echo "❌ SQL PREPARE ERROR: " . $conn->error . "\n";
        return false;
    }

    $stmt->bind_param("ssss", $source, $topic, $level, $message);
    
    if (!$stmt->execute()) {
        echo "❌ SQL EXEC ERROR: " . $stmt->error . "\n";
        $stmt->close();
        return false;
    }

    $insertId = $conn->insert_id;
    $stmt->close();
    
    echo "✅ LOG SAVED (ID: $insertId, Level: $level)\n";
    return true;
}