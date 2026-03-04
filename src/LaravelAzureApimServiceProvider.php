<?php

namespace Applab\LaravelAzureApim;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Applab\LaravelAzureApim\Commands\LaravelAzureApimCommand;

class LaravelAzureApimServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-azure-apim')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel_azure_apim_table')
            ->hasCommand(LaravelAzureApimCommand::class);
    }
}
