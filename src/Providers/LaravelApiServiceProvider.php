<?php

namespace Submtd\LaravelApi\Providers;

use Illuminate\Support\ServiceProvider;
use Submtd\LaravelApi\Services\Api;

class LaravelApiServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('api', function () {
            return new Api();
        });
    }
}
