<?php

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = new Dotenv\Dotenv(__DIR__."/../");
$dotenv->load();

$client = new Berkayk\OneSignal\OneSignalClient(
    getenv('APP_ID'),
    getenv('REST_API_KEY'),
    getenv('USER_AUTH_KEY'));

echo $client->testCredentials();
$client->sendNotificationToUser(".","4bc5da02-1722-4fee-943d-c8b5ccd507a2");