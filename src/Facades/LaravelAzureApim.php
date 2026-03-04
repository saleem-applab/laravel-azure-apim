<?php

namespace Applab\LaravelAzureApim\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Applab\LaravelAzureApim\LaravelAzureApim
 */
class LaravelAzureApim extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Applab\LaravelAzureApim\LaravelAzureApim::class;
    }
}
