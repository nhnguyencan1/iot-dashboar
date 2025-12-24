<?php
/**
 * API: Get Automation Rules
 * GET /api/get_automation_rules.php
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require __DIR__ . "/../lib/db.php";

try {
    // Lấy tất cả rules
    $sql = "SELECT * FROM automation_rules ORDER BY is_active DESC, created_at DESC";
    $result = $conn->query($sql);
    
    $rules = [];
    while ($row = $result->fetch_assoc()) {
        $rules[] = [
            'id' => (int)$row['id'],
            'name' => $row['name'],
            'description' => $row['description'],
            'trigger_type' => $row['trigger_type'],
            'trigger_operator' => $row['trigger_operator'],
            'trigger_value' => $row['trigger_value'],
            'trigger_value2' => $row['trigger_value2'],
            'action_type' => $row['action_type'],
            'action_value' => $row['action_value'],
            'is_active' => (bool)$row['is_active'],
            'last_triggered' => $row['last_triggered'],
            'trigger_count' => (int)$row['trigger_count'],
            'created_at' => $row['created_at']
        ];
    }
    
    echo json_encode($rules);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

$conn->close();