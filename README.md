🏠 IoT Smart Home Dashboard - Hệ thống Quản lý Nhà Thông minh

> Đồ án tốt nghiệp - Đại học Bách Khoa TP.HCM

## 📖 Giới thiệu

Hệ thống quản lý nhà thông minh sử dụng ESP32, cảm biến DHT22, PIR, và giao thức MQTT để giám sát và điều khiển thiết bị tự động.

## ✨ Tính năng chính

### 1. Dashboard Real-time
- 📊 Hiển thị nhiệt độ, độ ẩm theo thời gian thực
- 🌡️ Biểu đồ lịch sử 24 giờ
- 🚶 Phát hiện chuyển động
- 🔥 Cảnh báo cháy

### 2. Automation Rules (Auto-Revert)
- 🤖 Tạo quy tắc tự động hóa
- 🔄 Tự động đảo ngược khi điều kiện thay đổi
  - VD: Bật đèn khi có người → **Tự động tắt** khi không có người
- ⏰ Hỗ trợ nhiều loại trigger: Motion, Light, Temperature, Time
- 📝 Lịch sử kích hoạt chi tiết

### 3. Device Control
- 💡 Điều khiển 4 đèn độc lập
- 🚪 Điều khiển cửa
- 🔔 Còi báo động
- 🎛️ Giao diện trực quan

### 4. Activity Logs
- 📜 Lịch sử hoạt động đầy đủ
- 🔍 Lọc theo rule, thời gian
- 📊 Thống kê số lần kích hoạt

## 🛠️ Công nghệ sử dụng

### Backend
- **PHP 7.4+** - Server-side logic
- **MySQL** - Database
- **phpMQTT** - MQTT Client

### Frontend
- **Bootstrap 4** - UI Framework
- **jQuery** - DOM manipulation & AJAX
- **Chart.js** - Data visualization
- **Font Awesome** - Icons

### Hardware
- **ESP32** - Microcontroller
- **DHT22** - Temperature & Humidity sensor
- **PIR Sensor** - Motion detection
- **Flame Sensor** - Fire detection
- **LDR** - Light sensor
- **Relay Module** - Device control

### Communication
- **MQTT Protocol** - IoT messaging
- **Mosquitto Broker** - Message broker

## 📦 Cài đặt

### Yêu cầu
- XAMPP (Apache + MySQL + PHP)
- Mosquitto MQTT Broker
- ESP32 với Arduino IDE

### Bước 1: Clone project
```bashgit clone https://github.com/nhnguyencan1/iot-dashboar.git
cd iot-dashboar

### Bước 2: Import Database
1. Mở phpMyAdmin
2. Tạo database `iot_smarthome`
3. Import file `database/schema.sql`

### Bước 3: Cấu hình
Sửa file `lib/db.php`:
```php$servername = "localhost";
$username = "root";
$password = "";
$dbname = "iot_smarthome";

### Bước 4: Chạy MQTT Receiver
```bashphp mqtt/mqtt_receiver.php

### Bước 5: Truy cậphttp://localhost/iot-dashboar/

## 🎯 Cấu trúc Projectiot-dashboar/
├── api/                    # REST API endpoints
│   ├── get_automation_rules.php
│   ├── save_automation_rule.php
│   ├── get_automation_logs.php
│   └── ...
├── lib/                    # Core libraries
│   ├── db.php             # Database connection
│   └── rule_engine.php    # Automation engine
├── mqtt/                   # MQTT integration
│   └── mqtt_receiver.php  # MQTT message handler
├── js/                     # JavaScript
│   ├── automation.js      # Automation UI
│   └── main.js            # Dashboard
├── css/                    # Stylesheets
├── index.php              # Dashboard
├── automation.php         # Automation management
└── logs.php              # Activity logs

## 📊 Database Schema

### automation_rules
Lưu trữ các quy tắc tự động hóa
```sql
id, name, description
trigger_type, trigger_operator, trigger_value
action_type, action_value
auto_revert (Tính năng độc đáo!)
is_active, trigger_count


### automation_logs
Lịch sử kích hoạt rules
```sql
id, rule_id, rule_name
trigger_value_actual, action_executed
status, log_type (triggered/reverted)
created_at


### sensor_data
Dữ liệu cảm biến DHT22
```sql
id, temperature, humidity
created_at


## 🎬 Demo

### 1. Auto-Revert Feature (Tính năng nổi bật)Scenario: Bật đèn khi có ngườiTrigger: Motion = detected
Action: Light1 = ON
Auto-Revert: ✅ EnabledKết quả:

Có người đi qua → Đèn BẬT tự động
Không có người → Đèn TẮT tự động  ← Không cần tạo rule thứ 2!


### 2. Temperature ControlTrigger: Temperature > 30°C
Action: Fan = ON
Auto-Revert: ✅ EnabledKết quả:

Nóng > 30°C → Quạt BẬT
Mát ≤ 30°C → Quạt TẮT tự động


## 📸 Screenshots

### Dashboard
![Dashboard](docs/dashboard.png)
*Real-time monitoring với Chart.js*

### Automation Rules
![Automation](docs/automation.png)
*Quản lý rules với Auto-Revert*

### Activity Logs
![Logs](docs/logs.png)
*Lịch sử kích hoạt chi tiết*

## 🎓 Đóng góp học thuật

### Điểm mới so với các đồ án khác:
1. **Auto-Revert Mechanism**: Tự động đảo ngược hành động khi điều kiện không còn đúng
2. **Real-time Updates**: AJAX polling thông minh với adaptive refresh
3. **Comprehensive Logging**: Phân biệt triggered vs reverted
4. **User-friendly UI**: Giao diện trực quan, dễ sử dụng

## 👨‍💻 Tác giả

**Nguyễn Cần** - Sinh viên Đại học Bách Khoa TP.HCM

- GitHub: [@nhnguyencan1](https://github.com/nhnguyencan1)
- Email: nhnguyencan1@gmail.com

## 📝 License

MIT License

## 🙏 Cảm ơn

- Giảng viên hướng dẫn
- Khoa Điện - Điện tử, Đại học Bách Khoa TP.HCM
- Cộng đồng Arduino & ESP32