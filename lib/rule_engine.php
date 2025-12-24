<?php
/**
 * Rule Engine - Automation Rules Processor (WITH AUTO-REVERT)
 * Smart Home HCMUT
 * 
 * Xử lý các automation rules dựa trên sensor data từ MQTT
 * Hỗ trợ auto-revert khi điều kiện không còn đúng
 */

/**
 * Chạy tất cả rules dựa trên dữ liệu nhận được
 * 
 * @param mysqli $conn Database connection
 * @param array $data Dữ liệu từ MQTT (có thể là sensor data hoặc topic => message)
 */
function run_rules($conn, $data) {
    
    // Xử lý DHT sensor data
    if (isset($data['temp']) || isset($data['humi'])) {
        if (isset($data['temp'])) {
            process_trigger($conn, 'temperature', floatval($data['temp']));
        }
        if (isset($data['humi'])) {
            process_trigger($conn, 'humidity', floatval($data['humi']));
        }
        return;
    }
    
    // Xử lý các topic khác
    foreach ($data as $topic => $message) {
        
        // PIR Motion sensor
        if ($topic === 'fingerprint/pir' || strpos($topic, 'pir') !== false) {
            $value = ($message === 'motion' || $message === '1' || $message === 'detected') ? 'detected' : 'none';
            process_trigger($conn, 'motion', $value);
        }
        
        // Flame sensor
        elseif ($topic === 'fingerprint/flame' || strpos($topic, 'flame') !== false) {
            $value = ($message === 'fire' || $message === '1' || $message === 'detected') ? 'detected' : 'none';
            process_trigger($conn, 'fire', $value);
        }
        
        // Light sensor
        elseif ($topic === 'fingerprint/light_sensor' || strpos($topic, 'light_sensor') !== false) {
            $value = ($message === 'bright' || $message === '1') ? 'bright' : 'dark';
            process_trigger($conn, 'light', $value);
        }
    }
}

/**
 * Xử lý trigger VÀ auto-revert
 * 
 * @param mysqli $conn Database connection
 * @param string $triggerType Loại trigger (temperature, humidity, motion, light, fire, time)
 * @param mixed $actualValue Giá trị thực tế
 */
function process_trigger($conn, $triggerType, $actualValue) {
    
    // Lấy các rules đang active với trigger type tương ứng
    $stmt = $conn->prepare(
        "SELECT * FROM automation_rules WHERE is_active = 1 AND trigger_type = ?"
    );
    $stmt->bind_param("s", $triggerType);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($rule = $result->fetch_assoc()) {
        $conditionMet = check_condition($rule, $actualValue);
        
        // ✅ Điều kiện ĐÚng → Thực hiện action
        if ($conditionMet) {
            $success = execute_action($conn, $rule, false); // false = normal action
            log_trigger($conn, $rule, $actualValue, $success, 'triggered');
            update_rule_stats($conn, $rule['id'], true);
            
            echo "🤖 Rule TRIGGERED: {$rule['name']} | {$triggerType} = {$actualValue}\n";
        }
        // 🔄 Điều kiện SAI + Auto-revert enabled → Thực hiện reverse action
        elseif ($rule['auto_revert'] == 1) {
            $success = execute_action($conn, $rule, true); // true = reverse action
            
            // Chỉ log nếu thực sự có action được thực hiện
            if ($success) {
                log_trigger($conn, $rule, $actualValue, $success, 'reverted');
                echo "🔄 Rule REVERTED: {$rule['name']} | {$triggerType} = {$actualValue}\n";
            }
        }
    }
    
    $stmt->close();
}

/**
 * Kiểm tra điều kiện của rule
 * 
 * @param array $rule Rule data
 * @param mixed $actualValue Giá trị thực tế
 * @return bool
 */
function check_condition($rule, $actualValue) {
    $operator = $rule['trigger_operator'];
    $targetValue = $rule['trigger_value'];
    $targetValue2 = $rule['trigger_value2'];
    
    // Kiểm tra xem có phải so sánh số không
    $isNumeric = is_numeric($actualValue) && is_numeric($targetValue);
    
    if ($isNumeric) {
        $actualValue = floatval($actualValue);
        $targetValue = floatval($targetValue);
        if ($targetValue2) {
            $targetValue2 = floatval($targetValue2);
        }
    }
    
    switch ($operator) {
        case '=':
            return $actualValue == $targetValue;
        case '>':
            return $isNumeric && $actualValue > $targetValue;
        case '<':
            return $isNumeric && $actualValue < $targetValue;
        case '>=':
            return $isNumeric && $actualValue >= $targetValue;
        case '<=':
            return $isNumeric && $actualValue <= $targetValue;
        case 'between':
            return $isNumeric && $actualValue >= $targetValue && $actualValue <= $targetValue2;
        default:
            return false;
    }
}

/**
 * Thực hiện action của rule (hoặc reverse action)
 * 
 * @param mysqli $conn Database connection
 * @param array $rule Rule data
 * @param bool $reverse Có thực hiện reverse action không
 * @return bool Success status
 */
function execute_action($conn, $rule, $reverse = false) {
    $actionType = $rule['action_type'];
    $actionValue = $rule['action_value'];
    
    // 🔄 Đảo ngược action nếu cần
    if ($reverse) {
        $actionValue = ($actionValue === 'on') ? 'off' : 'on';
    }
    
    $command = build_mqtt_command($actionType, $actionValue);
    
    if ($command) {
        return publish_mqtt_command($command);
    }
    
    return false;
}

/**
 * Xây dựng MQTT command từ action
 * 
 * @param string $actionType Loại thiết bị
 * @param string $actionValue on/off
 * @return string|null MQTT command
 */
function build_mqtt_command($actionType, $actionValue) {
    $state = ($actionValue === 'on') ? 'on' : 'off';
    
    switch ($actionType) {
        case 'light1':
            return "light1_{$state}";
        case 'light2':
            return "light2_{$state}";
        case 'light3':
            return "light3_{$state}";
        case 'light4':
            return "light4_{$state}";
        case 'door':
            return ($actionValue === 'on') ? 'door_open' : 'door_close';
        case 'buzzer':
            return "buzzer_{$state}";
        case 'all_lights':
            return "all_lights_{$state}";
        default:
            return null;
    }
}

/**
 * Gửi command qua MQTT
 * 
 * @param string $command MQTT command
 * @return bool
 */
function publish_mqtt_command($command) {
    global $mqtt;
    
    // Nếu có MQTT client global (từ mqtt_receiver.php)
    if (isset($mqtt) && $mqtt) {
        try {
            $mqtt->publish('fingerprint/cmd', $command, 0);
            echo "📤 MQTT Published: fingerprint/cmd -> {$command}\n";
            return true;
        } catch (Exception $e) {
            echo "❌ MQTT Publish failed: " . $e->getMessage() . "\n";
            return false;
        }
    }
    
    // Fallback: ghi log nếu không có MQTT client
    echo "⚠️ No MQTT client, command logged: {$command}\n";
    return true;
}

/**
 * Ghi log khi rule được kích hoạt hoặc reverted
 * 
 * @param mysqli $conn Database connection
 * @param array $rule Rule data
 * @param mixed $actualValue Giá trị trigger thực tế
 * @param bool $success Thành công hay không
 * @param string $type 'triggered' hoặc 'reverted'
 */
function log_trigger($conn, $rule, $actualValue, $success, $type = 'triggered') {
    $stmt = $conn->prepare(
        "INSERT INTO automation_logs 
        (rule_id, rule_name, trigger_type, trigger_value_actual, action_executed, status, log_type)
        VALUES (?, ?, ?, ?, ?, ?, ?)"
    );
    
    $ruleName = $rule['name'];
    $triggerType = $rule['trigger_type'];
    $action = $rule['action_type'] . '_' . $rule['action_value'];
    $status = $success ? 'success' : 'failed';
    $actualValueStr = strval($actualValue);
    
    $stmt->bind_param("issssss", 
        $rule['id'], 
        $ruleName,
        $triggerType,
        $actualValueStr, 
        $action,
        $status,
        $type  // 'triggered' hoặc 'reverted'
    );
    
    $stmt->execute();
    $stmt->close();
}

/**
 * Cập nhật thống kê rule
 * 
 * @param mysqli $conn Database connection
 * @param int $ruleId Rule ID
 * @param bool $isTriggered True = triggered, False = reverted
 */
function update_rule_stats($conn, $ruleId, $isTriggered = true) {
    if ($isTriggered) {
        $stmt = $conn->prepare(
            "UPDATE automation_rules 
             SET last_triggered = NOW(), trigger_count = trigger_count + 1 
             WHERE id = ?"
        );
    } else {
        // Chỉ update last_triggered cho revert (không tăng trigger_count)
        $stmt = $conn->prepare(
            "UPDATE automation_rules 
             SET last_triggered = NOW() 
             WHERE id = ?"
        );
    }
    
    $stmt->bind_param("i", $ruleId);
    $stmt->execute();
    $stmt->close();
}

/**
 * Lưu dữ liệu sensor DHT vào database
 * 
 * @param mysqli $conn Database connection
 * @param array $data DHT data với temp và humi
 */
function save_sensor_data($conn, $data) {
    $temp = isset($data['temp']) ? floatval($data['temp']) : null;
    $humi = isset($data['humi']) ? floatval($data['humi']) : null;
    
    if ($temp === null && $humi === null) {
        return;
    }
    
    $stmt = $conn->prepare(
        "INSERT INTO sensor_data (temperature, humidity, created_at) VALUES (?, ?, NOW())"
    );
    $stmt->bind_param("dd", $temp, $humi);
    $stmt->execute();
    $stmt->close();
}

/**
 * Xử lý time-based rules (gọi từ cron job)
 * 
 * @param mysqli $conn Database connection
 */
function process_time_rules($conn) {
    $currentTime = date('H:i');
    process_trigger($conn, 'time', $currentTime);
}