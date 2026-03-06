<?php

namespace Applab\LaravelAzureApim;

use Applab\LaravelAzureApim\Auth\AzureAuthService;
use Applab\LaravelAzureApim\Http\ApimHttpClient;
use Applab\LaravelAzureApim\Managers\ApiManager;
use Applab\LaravelAzureApim\Managers\MonitorManager;
use Applab\LaravelAzureApim\Managers\PolicyManager;
use Applab\LaravelAzureApim\Managers\ProductManager;
use Applab\LaravelAzureApim\Managers\SubscriptionManager;
use Applab\LaravelAzureApim\Services\ApiService;
use Applab\LaravelAzureApim\Services\MonitorService;
use Applab\LaravelAzureApim\Services\PolicyService;
use Applab\LaravelAzureApim\Services\ProductService;
use Applab\LaravelAzureApim\Services\SubscriptionService;
use Illuminate\Support\ServiceProvider;

class ApimServiceProvider extends ServiceProvider
{
    /**
     * Register all package bindings.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/apim.php',
            'apim'
        );

        // Auth & HTTP
        $this->app->singleton(AzureAuthService::class);
        $this->app->singleton(ApimHttpClient::class);

        // Services
        $this->app->singleton(ApiService::class);
        $this->app->singleton(PolicyService::class);
        $this->app->singleton(ProductService::class);
        $this->app->singleton(SubscriptionService::class);
        $this->app->singleton(MonitorService::class);

        // Managers
        $this->app->singleton(ApiManager::class);
        $this->app->singleton(PolicyManager::class);
        $this->app->singleton(ProductManager::class);
        $this->app->singleton(SubscriptionManager::class);
        $this->app->singleton(MonitorManager::class);

        // Root manager (facade backing)
        $this->app->singleton(ApimManager::class);
    }

    /**
     * Bootstrap package services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/apim.php' => config_path('apim.php'),
            ], 'apim-config');
        }
    }
}
