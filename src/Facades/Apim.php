<?php

namespace Applab\LaravelAzureApim\Facades;

use Illuminate\Support\Facades\Facade;
use Applab\LaravelAzureApim\ApimManager;
use Applab\LaravelAzureApim\Managers\ApiManager;
use Applab\LaravelAzureApim\Managers\MonitorManager;
use Applab\LaravelAzureApim\Managers\PolicyManager;
use Applab\LaravelAzureApim\Managers\ProductManager;
use Applab\LaravelAzureApim\Managers\SubscriptionManager;

/**
 * @method static ApiManager          api()
 * @method static PolicyManager       policy()
 * @method static ProductManager      product()
 * @method static SubscriptionManager subscription()
 * @method static MonitorManager      monitor()
 *
 * @see \Applab\LaravelAzureApim\ApimManager
 */
class Apim extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ApimManager::class;
    }
}
