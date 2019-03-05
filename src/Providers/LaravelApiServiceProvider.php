<?php

namespace Submtd\LaravelApi\Providers;

use Illuminate\Support\ServiceProvider;
use Submtd\LaravelApi\Services\Api;

class LaravelApiServiceProvider extends ServiceProvider
{
    /**
     * register method
     */
    public function register()
    {
        // bind api class to the service container
        $this->app->bind('api', function () {
            return new Api();
        });
    }

    /**
     * boot method
     */
    public function boot()
    {
        // config files
        $this->mergeConfigFrom(__DIR__ . '/../../config/laravel-api.php', 'laravel-api');
        $this->publishes([__DIR__ . '/../../config' => config_path()], 'config');
    }
}
