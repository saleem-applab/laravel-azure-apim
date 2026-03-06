<?php

// config for Applab/LaravelAzureApim
return [

    /*
    |--------------------------------------------------------------------------
    | Azure Service Principal Credentials
    |--------------------------------------------------------------------------
    */
    'tenant_id'     => env('AZURE_TENANT_ID'),
    'client_id'     => env('AZURE_CLIENT_ID'),
    'client_secret' => env('AZURE_CLIENT_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Azure Subscription & Resource
    |--------------------------------------------------------------------------
    */
    'subscription_id' => env('AZURE_SUBSCRIPTION_ID'),
    'resource_group'  => env('AZURE_RESOURCE_GROUP'),
    'service_name'    => env('AZURE_APIM_SERVICE_NAME'),

    /*
    |--------------------------------------------------------------------------
    | Azure Management API Version
    |--------------------------------------------------------------------------
    */
    'api_version' => env('AZURE_APIM_API_VERSION', '2022-08-01'),

    /*
    |--------------------------------------------------------------------------
    | HTTP Client Settings
    |--------------------------------------------------------------------------
    */
    'timeout'        => env('AZURE_APIM_TIMEOUT', 30),
    'retry_times'    => env('AZURE_APIM_RETRY_TIMES', 3),
    'retry_sleep_ms' => env('AZURE_APIM_RETRY_SLEEP_MS', 500),

    /*
    |--------------------------------------------------------------------------
    | Token Cache TTL (seconds)
    |--------------------------------------------------------------------------
    */
    'token_cache_ttl' => env('AZURE_APIM_TOKEN_CACHE_TTL', 3500),

];
