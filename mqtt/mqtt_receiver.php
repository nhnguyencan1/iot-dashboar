<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . "/../vendor/autoload.php";
require __DIR__ . "/../lib/db.php";
require __DIR__ . "/../lib/save_fingerprint_log.php";
require __DIR__ . "/../lib/rule_engine.php";
require __DIR__ . "/../lib/save_event.php";

use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
use PhpMqtt\Client\Exceptions\ConnectingToBrokerFailedException;

// ================= DEBUG DB CONNECTION =================
if (!$conn) {
    die("❌ DB Connection is NULL!\n");
}
if ($conn->connect_error) {
    die("❌ DB Connect Error: " . $conn->connect_error . "\n");
}
echo "✅ DB Connected successfully\n";

// ================= CONFIG =================
$server   = '7ee24ad9420048c39cd09f7ffc7d4b14.s1.eu.hivemq.cloud';
$port     = 8883;
$username = 'nguyen';
$password = 'Nguyen123';
$clientId = 'php-receiver-' . uniqid();

// ================= CREATE CLIENT =================
$mqtt = new MqttClient($server, $port, $clientId);

// ================= TLS SETTINGS =================
$connectionSettings = (new ConnectionSettings())
    ->setUsername($username)
    ->setPassword($password)
    ->setKeepAliveInterval(60)
    ->setUseTls(true)
    ->setTlsSelfSignedAllowed(true)   // DEV only
    ->setTlsVerifyPeer(false);        // DEV only

echo "Connecting to broker {$server}:{$port} ...\n";

try {
    $mqtt->connect($connectionSettings, true);
    echo "Connected!\n";
} catch (ConnectingToBrokerFailedException $e) {
    die("Could not connect: " . $e->getMessage() . "\n");
}

// ================= SUBSCRIBE =================
$topics = [
    "fingerprint/#"
];

foreach ($topics as $topic) {
    $mqtt->subscribe($topic, function ($topic, $message, $retained) use ($conn) {

        $time = date('Y-m-d H:i:s');
        echo "[$time] $topic -> $message\n";
        
        if ($retained) {
            return;
        }
        
        // Truyền $conn vào save_event
        save_event($conn, $topic, $message);

        // ================= DELETE 1 FINGER =================
        if ($topic === "fingerprint/delete/ack") {
            if (str_starts_with(trim($message), "OK:")) {
                $id = intval(substr(trim($message), 3));
                if ($id > 0) {
                    $conn->query("DELETE FROM fingerprints WHERE id = $id");
                    echo ">>> DB deleted fingerprint ID=$id\n";
                }
            }
            return;
        }

        // ================= DELETE ALL =================
        if ($topic === "fingerprint/delete_all/ack") {
            if (trim($message) === "OK") {
                $conn->query("DELETE FROM fingerprints");
                echo ">>> DB deleted ALL fingerprints\n";
            }
            return;
        }

        if ($topic === "fingerprint/id") {
            $fingerId = intval($message);
            if ($fingerId > 0) {
                save_fingerprint_log($conn, $fingerId, "matched");
            }
            return;
        }

        if ($topic === "fingerprint/dht") {
            $data = json_decode($message, true);
            if ($data !== null) {
                save_sensor_data($conn, $data);
                run_rules($conn, $data);
            }
            return;
        }

        run_rules($conn, [$topic => $message]);

    }, 0);
}

echo "Listening ...\n";

// ================= LOOP =================
$mqtt->loop(true);