#  OneSignal Push Notifications for Laravel 5

## Introduction

This is a simple OneSignal wrapper library for Laravel. It simplifies the basic notification flow with the defined methods. You can send a message to all users or you can notify a single user. 
Before you start installing this service, please complete your OneSignal setup at https://onesignal.com and finish all the steps that is necessary to obtain an application id and REST API Keys.


## Installation

First, you'll need to require the package with Composer:

```sh
composer require berkayk/onesignal-laravel
```

Aftwards, run `composer update` from your command line.

Then, update `config/app.php` by adding an entry for the service provider.

```php
'providers' => [
	// ...
	Berkayk\OneSignal\OneSignalServiceProvider::class
];
```


Then, register class alias by adding an entry in aliases section

```php
'aliases' => [
	// ...
	'OneSignal' => Berkayk\OneSignal\OneSignalFacade::class
];
```

Finally, from the command line again, run `php artisan vendor:publish` to publish the default configuration file. 
This will publish a configuration file named `onesignal.php` which includes your OneSignal authorization keys.



## Configuration

You need to fill in `onesignal.php` file that is found in your applications `config` directory.
`app_id` is your *OneSignal App ID* and `rest_api_key` is your *REST API Key*.

## Usage

### Sending a Notification To All Users

You can easily send a message to all registered users with the command

    OneSignal::sendNotificationToAll("Some Message");
    OneSignal::sendNotificationToAll("Some Message", $url);
    OneSignal::sendNotificationToAll("Some Message", $url, $data);
    
`$url` and `$data` fields are exceptional. If you provide a `$url` parameter, users will be redirecting to that url.
    

### Sending a Notification To A Specific User

After storing a user's tokens in a table, you can simply send a message with

    OneSignal::sendNotificationToUser("Some Message", $userId);
    OneSignal::sendNotificationToUser("Some Message", $userId, $url);
    OneSignal::sendNotificationToUser("Some Message", $userId, $url, $data);
    
`$userId` is the user's unique id where he/she is registered for notifications. Read https://documentation.onesignal.com/docs/website-sdk-api#getUserId for additional details.
`$url` and `$data` fields are exceptional. If you provide a `$url` parameter, users will be redirecting to that url.


### Sending a Notification To Segment

You can simply send a notification to a specific segment with

    OneSignal::sendNotificationToSegment("Some Message", $segment);
    OneSignal::sendNotificationToSegment("Some Message", $segment, $url);
    OneSignal::sendNotificationToSegment("Some Message", $segment, $url, $data);
    
`$url` and `$data` fields are exceptional. If you provide a `$url` parameter, users will be redirecting to that url.

### Sending a Custom Notification

You can send a custom message with 

    OneSignal::sendNotificationCustom($parameters);
    
Please refer to https://documentation.onesignal.com/docs/notifications-create-notification for all customizable parameters.

