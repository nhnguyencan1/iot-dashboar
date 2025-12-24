<?php
require __DIR__ . "/../vendor/autoload.php";

use PhpMqtt\Client\MqttClient;

function publish_cmd($cmd) {
    $server   = '7ee24ad9420048c39cd09f7ffc7d4b14.s1.eu.hivemq.cloud';
    $port     = 8883;
    $username = 'nguyen';
    $password = 'Nguyen123';

    $mqtt = new MqttClient($server, $port, "php-publisher-" . rand(), MqttClient::MQTT_3_1_1);

    $mqtt->setTlsSettings([
        'allow_self_signed' => true,
        'verify_peer'       => false,
        'verify_peer_name'  => false,
    ]);

    $mqtt->connectWithCredentials($username, $password, true);

    $mqtt->publish("fingerprint/cmd", $cmd, 0);

    echo "Published CMD → $cmd\n";

    $mqtt->disconnect();
}
