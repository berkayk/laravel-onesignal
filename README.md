#  OneSignal Push Notifications for Laravel 5

## Introduction

This is a simple OneSignal wrapper library for Laravel. It simplifies the basic notification flow with the defined methods. You can send a message to all users or you can notify a single user. 
Before you start installing this service, please complete your OneSignal setup at https://onesignal.com and finish all the steps that is necessary to obtain an application id and REST API Keys.


## Installation

First, you'll need to require the package with Composer:

```sh
composer require umuttaymaz/onesignal-laravel
```

Aftwards, run `composer update` from your command line.

Then, update `config/app.php` by adding an entry for the service provider.

```php
'providers' => [
	// ...
	UmutTaymaz\OneSignal\OneSignalServiceProvider::class
];
```


Then, register class alias by adding an entry in aliases section

```php
'aliases' => [
	// ...
	'OneSignal' => UmutTaymaz\OneSignal\OneSignalFacade::class
];
```


Finally, from the command line again, run 

```
php artisan vendor:publish --tag=config
``` 

to publish the default configuration file. 
This will publish a configuration file named `onesignal.php` which includes your OneSignal authorization keys.

> **Note:** If the previous command does not publish the config file successfully, please check the steps involving *providers* and *aliases* in the `config/app.php` file.


