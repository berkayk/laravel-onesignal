<?php

require_once __DIR__ . '/../vendor/autoload.php';

$client = new Berkayk\OneSignal\OneSignalClient("1","2","3");

echo $client->testCredentials();