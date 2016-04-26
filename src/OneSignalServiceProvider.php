<?php

namespace Berkayk\OneSignal;

use Illuminate\Support\ServiceProvider;

class OneSignalServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        $this->publishes([
            __DIR__.'/config/onesignal.php' => config_path('onesignal.php'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->singleton('onesignal', function () {

            $config = isset($app['config']['services']['onesignal']) ? $app['config']['services']['onesignal'] : null;
            if (is_null($config)) {
                $config = $app['config']['onesignal'] ?: $app['config']['onesignal::config'];
            }

            $client = new OneSignalClient($config['app_id'], $config['rest_api_key'], $config['user_auth_key']);

            return $client;
        });
    }

    public function provides() {
        return ['onesignal'];
    }


}
