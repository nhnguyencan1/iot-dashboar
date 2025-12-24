<?php
/**
 * API: Delete Automation Rule (FIXED)
 * POST /api/delete_automation_rule.php
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
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['id'])) {
        throw new Exception('Missing rule ID');
    }

    $id = (int)$input['id'];

    // ===============================
    // BEGIN TRANSACTION
    // ===============================
    $conn->begin_transaction();

    // 1️⃣ XÓA LOG TRƯỚC
    $stmt = $conn->prepare(
        "DELETE FROM automation_logs WHERE rule_id = ?"
    );
    $stmt->bind_param("i", $id);
    $stmt->execute();

    // 2️⃣ XÓA RULE
    $stmt = $conn->prepare(
        "DELETE FROM automation_rules WHERE id = ?"
    );
    $stmt->bind_param("i", $id);
    $stmt->execute();

    if ($stmt->affected_rows === 0) {
        $conn->rollback();
        throw new Exception('Rule not found');
    }

    $conn->commit();

    echo json_encode([
        'status'  => 'ok',
        'message' => 'Rule deleted successfully'
    ]);

} catch (Exception $e) {
    if ($conn->errno) {
        $conn->rollback();
    }

    http_response_code(400);
    echo json_encode([
        'status'  => 'error',
        'message' => $e->getMessage()
    ]);
}

$conn->close();
