<?php
/**
 * API: Save Automation Rule (Create / Update) - WITH AUTO-REVERT
 * POST /api/save_automation_rule.php
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require __DIR__ . '/../lib/db.php';

try {
    // ===============================
    // 1️⃣ READ JSON INPUT
    // ===============================
    $input = json_decode(file_get_contents('php://input'), true);

    if (!is_array($input)) {
        throw new Exception('Invalid JSON input');
    }

    // ===============================
    // 2️⃣ VALIDATION
    // ===============================
    $required = [
        'name', 'trigger_type', 'trigger_operator',
        'trigger_value', 'action_type', 'action_value'
    ];

    foreach ($required as $field) {
        if (!isset($input[$field]) || $input[$field] === '') {
            throw new Exception("Missing required field: $field");
        }
    }

    // ===============================
    // 3️⃣ SANITIZE / CAST
    // ===============================
    $id               = isset($input['id']) ? (int)$input['id'] : 0;
    $name             = trim($input['name']);
    $description      = trim($input['description'] ?? '');
    $trigger_type     = $input['trigger_type'];
    $trigger_operator = $input['trigger_operator'];
    $trigger_value    = $input['trigger_value'];
    $trigger_value2   = $input['trigger_value2'] ?? null;
    $action_type      = $input['action_type'];
    $action_value     = $input['action_value'];
    $auto_revert      = isset($input['auto_revert']) ? (int)$input['auto_revert'] : 0;  
    $is_active        = isset($input['is_active']) ? (int)$input['is_active'] : 1;

    // ===============================
    // 4️⃣ INSERT / UPDATE
    // ===============================
    if ($id > 0) {
        // UPDATE
        $stmt = $conn->prepare("
            UPDATE automation_rules SET
                name = ?, description = ?,
                trigger_type = ?, trigger_operator = ?, trigger_value = ?,
                trigger_value2 = ?,
                action_type = ?, action_value = ?,
                auto_revert = ?, is_active = ?
            WHERE id = ?
        ");

        $stmt->bind_param(
            "sssssssssii",
            $name, $description,
            $trigger_type, $trigger_operator, $trigger_value,
            $trigger_value2,
            $action_type, $action_value,
            $auto_revert,  
            $id
        );

        $stmt->execute();

        if ($stmt->affected_rows === 0 && $stmt->errno !== 0) {
            throw new Exception('Update failed');
        }

        $msg = 'Rule updated successfully';

    } else {
        // INSERT
        $stmt = $conn->prepare("
            INSERT INTO automation_rules
                (name, description, trigger_type, trigger_operator,
                 trigger_value, trigger_value2,
                 action_type, action_value, auto_revert, is_active)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "ssssssssii",
            $name, $description,
            $trigger_type, $trigger_operator,
            $trigger_value, $trigger_value2,
            $action_type, $action_value,
            $auto_revert,  // ✨ NEW
            $is_active
        );

        $stmt->execute();

        if ($stmt->insert_id === 0) {
            throw new Exception('Insert failed');
        }

        $msg = 'Rule created successfully';
        $id  = $stmt->insert_id;
    }

    // ===============================
    // 5️⃣ RESPONSE
    // ===============================
    echo json_encode([
        'status'  => 'ok',
        'message' => $msg,
        'id'      => $id
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status'  => 'error',
        'message' => $e->getMessage()
    ]);
}

$conn->close();