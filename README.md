#  OneSignal Push Notifications for Laravel 5

## Introduction

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

...



