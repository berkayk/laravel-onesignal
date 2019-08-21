#  OneSignal Push Notifications for Laravel 5

## Introduction

This is a simple OneSignal wrapper library for Laravel. It simplifies the basic
notification flow with the defined methods. You can send a message to all users
or you can notify a single user. Before you start installing this service, 
please complete your OneSignal setup at https://onesignal.com and finish all 
the steps that is necessary to obtain an application id and REST API Keys.


## Installation

First, you'll need to require the package with Composer:

```sh
composer require berkayk/onesignal-laravel
```

Afterwards, run `composer update` from your command line.

**You only need to do the following if your Laravel version is below 5.5**:

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


Finally, from the command line again, run 

```
php artisan vendor:publish --tag=config
``` 

to publish the default configuration file. 
This will publish a configuration file named `onesignal.php` which includes 
your OneSignal authorization keys.

> **Note:** If the previous command does not publish the config file successfully, 
> please check the steps involving *providers* and *aliases* in the `config/app.php` file.


## Configuration

You need to fill in `onesignal.php` file that is found in your applications `config` directory.
`app_id` is your *OneSignal App ID* and `rest_api_key` is your *REST API Key*.

## Usage

### Sending a Notification To All Users

You can easily send a message to all registered users with the command

```php
    OneSignal::sendNotificationToAll(
        "Some Message", 
        $url = null, 
        $data = null, 
        $buttons = null, 
        $schedule = null
    );
```
    
`$url` , `$data` , `$buttons` and `$schedule` fields are exceptional. If you 
provide a `$url` parameter, users will be redirecting to that url.
    

### Sending a Notification based on Tags/Filters

You can send a message based on a set of tags with the command

  ##### Example 1:

```php
    OneSignal::sendNotificationUsingTags(
        "Some Message",
        array(
            ["key" => "email", "relation" => "=", "value" => "email21@example.com"],
            ["key" => "email", "relation" => "=", "value" => "email1@example.com"],
            ...
        ),
        $url = null,
        $data = null,
        $buttons = null,
        $schedule = null
    );
```
    
  ##### Example 2:

```php
    OneSignal::sendNotificationUsingTags(
        "Some Message",
        array(
            ["key" => "session_count", "relation" => ">", "value" => '2'],
            ["key" => "first_session", "relation" => ">", "value" => '2000'],
        ),
        $url = null,
        $data = null,
        $buttons = null,
        $schedule = null
    );
```

### Sending a Notification To A Specific User

After storing a user's tokens in a table, you can simply send a message with

```php
    OneSignal::sendNotificationToUser(
        "Some Message",
        $userId,
        $url = null,
        $data = null,
        $buttons = null,
        $schedule = null
    );
```
    
`$userId` is the user's unique id where he/she is registered for notifications. 
Read https://documentation.onesignal.com/docs/web-push-tagging-guide for additional details.
`$url` , `$data` , `$buttons` and `$schedule` fields are exceptional. If you provide 
a `$url` parameter, users will be redirecting to that url.



### Sending a Notification To A Specific external User (custom user id added by user)

After storing a user's tokens in a table, you can simply send a message with

```php
    OneSignal::sendNotificationToExternalUser(
        "Some Message",
        $userId,
        $url = null,
        $data = null,
        $buttons = null,
        $schedule = null
    );
```

`$userId` is the user's unique external id (custom id) added by the user where he/she is registered for notifications.
Read https://documentation.onesignal.com/docs/web-push-tagging-guide for additional details.
`$url` , `$data` , `$buttons` and `$schedule` fields are exceptional. If you provide
a `$url` parameter, users will be redirecting to that url.

### Sending a Notification To Segment

You can simply send a notification to a specific segment with

```php
    OneSignal::sendNotificationToSegment(
        "Some Message",
        $segment,
        $url = null,
        $data = null,
        $buttons = null,
        $schedule = null
    );
```
    
`$url` , `$data` , `$buttons` and `$schedule` fields are exceptional. If you 
provide a `$url` parameter, users will be redirecting to that url.

### Sending a Custom Notification

You can send a custom message with 

```php
    OneSignal::sendNotificationCustom($parameters);
```
    
### Sending a Custom Notification

### Sending a async Custom Notification
You can send a async custom message with 

```php
    OneSignal::async()->sendNotificationCustom($parameters);
```    
    
Please refer to https://documentation.onesignal.com/reference for all customizable parameters.

