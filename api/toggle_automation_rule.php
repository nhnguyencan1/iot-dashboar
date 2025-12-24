<?php
/**
 * API: Toggle Automation Rule Active Status
 * POST /api/toggle_automation_rule.php
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

    if (!isset($input['id'])) {
        throw new Exception('Missing rule ID');
    }

    $id = (int)$input['id'];

    // ===============================
    // 2️⃣ CHECK RULE EXISTS
    // ===============================
    $stmt = $conn->prepare(
        "SELECT is_active FROM automation_rules WHERE id = ?"
    );
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $res = $stmt->get_result();
    if ($res->num_rows === 0) {
        throw new Exception('Rule not found');
    }

    $current = (int)$res->fetch_assoc()['is_active'];

    // ===============================
    // 3️⃣ TOGGLE VALUE (FIXED LOGIC)
    // ===============================
    $new = $current === 1 ? 0 : 1;

    // ===============================
    // 4️⃣ UPDATE DB
    // ===============================
    $stmt = $conn->prepare(
        "UPDATE automation_rules SET is_active = ? WHERE id = ?"
    );
    $stmt->bind_param("ii", $new, $id);
    $stmt->execute();

    if ($stmt->affected_rows === 0) {
        throw new Exception('Failed to update rule');
    }

    // ===============================
    // 5️⃣ RESPONSE
    // ===============================
    echo json_encode([
        'status'    => 'ok',
        'message'   => 'Rule toggled successfully',
        'is_active' => (bool)$new
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status'  => 'error',
        'message' => $e->getMessage()
    ]);
}

$conn->close();
