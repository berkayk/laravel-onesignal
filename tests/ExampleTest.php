<?php

ini_set("auto_detect_line_endings", false);

test('test notification', function () {
    require_once __DIR__ . '/../vendor/autoload.php';

    $dotenv = new Dotenv\Dotenv(__DIR__ . "/../");
    $dotenv->load();

    $client = new Berkayk\OneSignal\OneSignalClient(
        getenv('APP_ID'),
        getenv('REST_API_KEY'),
        getenv('USER_AUTH_KEY'));

    echo $client->testCredentials();
    $response = $client->sendNotificationToUser("this is test message", "4bc5da02-1722-4fee-943d-c8b5ccd507a2");
});
