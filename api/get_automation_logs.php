<?php
/**
 * API: Get Automation Logs
 * GET /api/get_automation_logs.php
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require __DIR__ . "/../lib/db.php";

try {
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
    $rule_id = isset($_GET['rule_id']) ? (int)$_GET['rule_id'] : null;
    
    $sql = "SELECT * FROM automation_logs";
    
    if ($rule_id) {
        $sql .= " WHERE rule_id = $rule_id";
    }
    
    $sql .= " ORDER BY created_at DESC LIMIT $limit";
    
    $result = $conn->query($sql);
    
    $logs = [];
    while ($row = $result->fetch_assoc()) {
        $logs[] = [
            'id' => (int)$row['id'],
            'rule_id' => (int)$row['rule_id'],
            'rule_name' => $row['rule_name'],
            'trigger_type' => $row['trigger_type'],
            'trigger_value_actual' => $row['trigger_value_actual'],
            'action_executed' => $row['action_executed'],
            'status' => $row['status'],
            'error_message' => $row['error_message'],
            'created_at' => $row['created_at']
        ];
    }
    
    echo json_encode($logs);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

$conn->close();