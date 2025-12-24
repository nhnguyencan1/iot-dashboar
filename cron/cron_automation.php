<?php
/**
 * Cron Job: Process Time-Based Automation Rules
 * 
 * Chạy mỗi phút để kiểm tra các rules theo thời gian
 * 
 * Crontab entry:
 * * * * * * php /var/www/html/iot-dashboar/cron/cron_automation.php >> /var/www/html/iot-dashboar/logs/cron.log 2>&1
 */

require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/rule_engine.php';

$time = date('Y-m-d H:i:s');
echo "[$time] Running time-based automation check...\n";

if (!$conn || $conn->connect_error) {
    echo "[$time] ERROR: Database connection failed\n";
    exit(1);
}

try {
    process_time_rules($conn);
    echo "[$time] Time rules processed successfully\n";
} catch (Exception $e) {
    echo "[$time] ERROR: " . $e->getMessage() . "\n";
}

$conn->close();