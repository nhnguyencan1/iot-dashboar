<?php
/**
 * Automation Engine
 * Xử lý các automation rules dựa trên sensor data
 * 
 * Được gọi từ mqtt_receiver.php khi có dữ liệu sensor mới
 */

require __DIR__ . "/lib/db.php";

class AutomationEngine {
    
    private $conn;
    private $mqttClient;
    
    public function __construct($conn, $mqttClient = null) {
        $this->conn = $conn;
        $this->mqttClient = $mqttClient;
    }
    
    /**
     * Xử lý tất cả rules dựa trên trigger type và value
     */
    public function processRules($triggerType, $actualValue) {
        // Lấy các rules active với trigger type tương ứng
        $sql = "SELECT * FROM automation_rules 
                WHERE is_active = 1 AND trigger_type = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $triggerType);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $triggeredRules = [];
        
        while ($rule = $result->fetch_assoc()) {
            if ($this->checkCondition($rule, $actualValue)) {
                // Rule được kích hoạt
                $success = $this->executeAction($rule);
                $this->logTrigger($rule, $actualValue, $success);
                $this->updateRuleStats($rule['id']);
                $triggeredRules[] = $rule;
            }
        }
        
        $stmt->close();
        return $triggeredRules;
    }
    
    /**
     * Kiểm tra điều kiện của rule
     */
    private function checkCondition($rule, $actualValue) {
        $operator = $rule['trigger_operator'];
        $targetValue = $rule['trigger_value'];
        $targetValue2 = $rule['trigger_value2'];
        
        // Chuyển đổi sang số nếu cần
        $isNumeric = is_numeric($actualValue) && is_numeric($targetValue);
        
        if ($isNumeric) {
            $actualValue = floatval($actualValue);
            $targetValue = floatval($targetValue);
            if ($targetValue2) $targetValue2 = floatval($targetValue2);
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
     * Thực hiện action của rule
     */
    private function executeAction($rule) {
        $actionType = $rule['action_type'];
        $actionValue = $rule['action_value'];
        
        $command = $this->buildCommand($actionType, $actionValue);
        
        if ($command) {
            return $this->sendMqttCommand($command);
        }
        
        return false;
    }
    
    /**
     * Xây dựng MQTT command từ action
     */
    private function buildCommand($actionType, $actionValue) {
        $commands = [];
        
        switch ($actionType) {
            case 'light1':
                return $actionValue === 'on' ? 'light1_on' : 'light1_off';
            case 'light2':
                return $actionValue === 'on' ? 'light2_on' : 'light2_off';
            case 'light3':
                return $actionValue === 'on' ? 'light3_on' : 'light3_off';
            case 'light4':
                return $actionValue === 'on' ? 'light4_on' : 'light4_off';
            case 'door':
                return $actionValue === 'on' ? 'door_open' : 'door_close';
            case 'buzzer':
                return $actionValue === 'on' ? 'buzzer_on' : 'buzzer_off';
            case 'all_lights':
                // Trả về mảng commands
                $state = $actionValue === 'on' ? 'on' : 'off';
                return "all_lights_{$state}";
            default:
                return null;
        }
    }
    
    /**
     * Gửi command qua MQTT
     */
    private function sendMqttCommand($command) {
        if ($this->mqttClient && $this->mqttClient->isConnected()) {
            try {
                $this->mqttClient->publish('fingerprint/cmd', $command, 0);
                return true;
            } catch (Exception $e) {
                error_log("MQTT publish failed: " . $e->getMessage());
                return false;
            }
        }
        
        // Fallback: Log command nếu không có MQTT client
        error_log("Automation command (no MQTT): $command");
        return true;
    }
    
    /**
     * Ghi log khi rule được kích hoạt
     */
    private function logTrigger($rule, $actualValue, $success) {
        $sql = "INSERT INTO automation_logs 
                (rule_id, rule_name, trigger_type, trigger_value_actual, action_executed, status)
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        $ruleName = $rule['name'];
        $triggerType = $rule['trigger_type'];
        $action = $rule['action_type'] . '_' . $rule['action_value'];
        $status = $success ? 'success' : 'failed';
        
        $stmt->bind_param("isssss", 
            $rule['id'], 
            $ruleName,
            $triggerType,
            $actualValue, 
            $action,
            $status
        );
        
        $stmt->execute();
        $stmt->close();
    }
    
    /**
     * Cập nhật thống kê rule
     */
    private function updateRuleStats($ruleId) {
        $sql = "UPDATE automation_rules 
                SET last_triggered = NOW(), trigger_count = trigger_count + 1 
                WHERE id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $ruleId);
        $stmt->execute();
        $stmt->close();
    }
    
    /**
     * Xử lý rules theo thời gian (chạy mỗi phút từ cron)
     */
    public function processTimeRules() {
        $currentTime = date('H:i');
        return $this->processRules('time', $currentTime);
    }
    
    /**
     * Xử lý khi có dữ liệu nhiệt độ
     */
    public function onTemperatureChange($temp) {
        return $this->processRules('temperature', $temp);
    }
    
    /**
     * Xử lý khi có dữ liệu độ ẩm
     */
    public function onHumidityChange($humidity) {
        return $this->processRules('humidity', $humidity);
    }
    
    /**
     * Xử lý khi phát hiện chuyển động
     */
    public function onMotionDetected($hasMotion) {
        $value = $hasMotion ? 'detected' : 'none';
        return $this->processRules('motion', $value);
    }
    
    /**
     * Xử lý khi thay đổi ánh sáng
     */
    public function onLightChange($isBright) {
        $value = $isBright ? 'bright' : 'dark';
        return $this->processRules('light', $value);
    }
    
    /**
     * Xử lý khi phát hiện cháy
     */
    public function onFireDetected($hasFire) {
        if ($hasFire) {
            return $this->processRules('fire', 'detected');
        }
        return [];
    }
}

/**
 * Helper function để sử dụng trong mqtt_receiver.php
 */
function runAutomation($conn, $triggerType, $value, $mqttClient = null) {
    $engine = new AutomationEngine($conn, $mqttClient);
    
    switch ($triggerType) {
        case 'temperature':
            return $engine->onTemperatureChange($value);
        case 'humidity':
            return $engine->onHumidityChange($value);
        case 'motion':
            return $engine->onMotionDetected($value);
        case 'light':
            return $engine->onLightChange($value);
        case 'fire':
            return $engine->onFireDetected($value);
        default:
            return $engine->processRules($triggerType, $value);
    }
}