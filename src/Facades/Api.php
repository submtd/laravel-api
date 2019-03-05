<?php

namespace Submtd\LaravelApi\Facades;

use Illuminate\Support\Facades\Facade;

class Api extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'api';
    }
}
