# Laravel Azure APIM

[![Latest Version on Packagist](https://img.shields.io/packagist/v/applab/laravel-azure-apim.svg?style=flat-square)](https://packagist.org/packages/applab/laravel-azure-apim)
[![Total Downloads](https://img.shields.io/packagist/dt/applab/laravel-azure-apim.svg?style=flat-square)](https://packagist.org/packages/applab/laravel-azure-apim)

A production-ready Laravel package for integrating with **Microsoft Azure API Management (APIM)**. It provides a clean DTO + Service Layer architecture to manage APIs, policies, products, subscriptions, and monitoring directly from Laravel applications.

---

## Features

- ✅ **API Management** — Create, update, delete, list APIs
- ✅ **Policy Management** — Apply throttle (rate-limit) policies and custom XML policies
- ✅ **Product Management** — Create, update, delete products; assign APIs to products
- ✅ **Subscription Management** — Subscribe / unsubscribe users to products
- ✅ **Monitoring & Analytics** — API usage, product usage, error statistics
- ✅ **Azure AD OAuth2** — Client-credentials token caching
- ✅ **Retry mechanism** — Automatic retry with back-off on `429 Too Many Requests`
- ✅ **Structured exceptions** — `ApimException`, `ApimAuthException`

---

## Requirements

- PHP `^8.4`
- Laravel `^11.0` or `^12.0`

---

## Installation

```bash
composer require applab/laravel-azure-apim
```

### Publish the configuration file

```bash
php artisan vendor:publish --tag=apim-config
```

---

## Configuration

Add the following environment variables to your `.env` file:

```dotenv
AZURE_TENANT_ID=your-tenant-id
AZURE_CLIENT_ID=your-client-id
AZURE_CLIENT_SECRET=your-client-secret
AZURE_SUBSCRIPTION_ID=your-subscription-id
AZURE_RESOURCE_GROUP=your-resource-group
AZURE_APIM_SERVICE_NAME=your-apim-service-name

# Optional (defaults shown)
AZURE_APIM_API_VERSION=2022-08-01
AZURE_APIM_TIMEOUT=30
AZURE_APIM_RETRY_TIMES=3
AZURE_APIM_RETRY_SLEEP_MS=500
AZURE_APIM_TOKEN_CACHE_TTL=3500
```

The published `config/apim.php` file maps all values from the environment.

---

## Usage

All operations are available via the `Apim` facade.

### API Management

```php
use Applab\LaravelAzureApim\Facades\Apim;
use Applab\LaravelAzureApim\DTO\CreateApiData;
use Applab\LaravelAzureApim\DTO\UpdateApiData;

// Create an API
Apim::api()->create(
    CreateApiData::from([
        'apiId'                => 'weather-api',
        'openApiSpecification' => $openApiJson,
        'path'                 => 'weather',
        'backendUrl'           => 'https://provider.com',
        'displayName'          => 'Weather API',   // optional
    ])
);

// Update an API
Apim::api()->update(
    UpdateApiData::from([
        'apiId'      => 'weather-api',
        'backendUrl' => 'https://new-provider.com',
    ])
);

// Delete an API
Apim::api()->delete('weather-api');

// List all APIs
$apis = Apim::api()->list();
```

### Policy Management

```php
use Applab\LaravelAzureApim\Facades\Apim;
use Applab\LaravelAzureApim\DTO\ThrottlePolicyData;

// Apply a rate-limit (throttle) policy
Apim::policy()->applyThrottle(
    ThrottlePolicyData::from([
        'apiId'         => 'weather-api',
        'calls'         => 100,
        'renewalPeriod' => 60,
    ])
);

// Apply a custom XML policy
Apim::policy()->applyCustomPolicy('weather-api', $xmlString);

// Get the current policy for an API
$policy = Apim::policy()->getPolicy('weather-api');

// Remove a policy
Apim::policy()->deletePolicy('weather-api');
```

### Product Management

```php
use Applab\LaravelAzureApim\Facades\Apim;
use Applab\LaravelAzureApim\DTO\CreateProductData;
use Applab\LaravelAzureApim\DTO\AssignApiToProductData;

// Create a product
Apim::product()->create(
    CreateProductData::from([
        'productId'   => 'weather-basic',
        'displayName' => 'Weather Basic',
    ])
);

// Assign an API to a product
Apim::product()->assignApi(
    AssignApiToProductData::from([
        'productId' => 'weather-basic',
        'apiId'     => 'weather-api',
    ])
);

// Delete a product
Apim::product()->delete('weather-basic');

// List all products
$products = Apim::product()->list();
```

### Subscription Management

```php
use Applab\LaravelAzureApim\Facades\Apim;
use Applab\LaravelAzureApim\DTO\CreateSubscriptionData;

// Subscribe a user to a product
Apim::subscription()->subscribe(
    CreateSubscriptionData::from([
        'subscriptionId' => 'sub-1',
        'productId'      => 'weather-basic',
        'userId'         => 'user-1',
    ])
);

// Unsubscribe
Apim::subscription()->unsubscribe('sub-1');

// List all subscriptions
$subs = Apim::subscription()->list();
```

### Monitoring & Analytics

```php
use Applab\LaravelAzureApim\Facades\Apim;

// Get usage stats for an API
$usage = Apim::monitor()->apiUsage('weather-api');

// Get usage stats for a product
$usage = Apim::monitor()->productUsage('weather-basic');

// Get error statistics across all APIs
$errors = Apim::monitor()->apiErrors();

// By subscription
$stats = Apim::monitor()->bySubscription();

// By time interval (ISO 8601 duration)
$stats = Apim::monitor()->byTime('PT1H');
```

---

## DTO Reference

| DTO | Properties |
|-----|------------|
| `CreateApiData` | `apiId`, `openApiSpecification`, `path`, `backendUrl`, `displayName`, `format` |
| `UpdateApiData` | `apiId`, `backendUrl`, `displayName`, `path` |
| `CreateProductData` | `productId`, `displayName`, `description`, `state`, `subscriptionRequired` |
| `AssignApiToProductData` | `productId`, `apiId` |
| `CreateSubscriptionData` | `subscriptionId`, `productId`, `userId`, `displayName`, `state` |
| `ThrottlePolicyData` | `apiId`, `calls`, `renewalPeriod`, `counterKey` |

All DTOs expose a static `from(array $data)` factory method.

---

## Architecture

```
src/
├── Auth/
│   └── AzureAuthService.php       # OAuth2 client-credentials + token cache
├── Http/
│   └── ApimHttpClient.php         # Base URL, bearer token, retry, error handling
├── DTO/
│   ├── CreateApiData.php
│   ├── UpdateApiData.php
│   ├── CreateProductData.php
│   ├── AssignApiToProductData.php
│   ├── CreateSubscriptionData.php
│   └── ThrottlePolicyData.php
├── Services/
│   ├── ApiService.php
│   ├── PolicyService.php
│   ├── ProductService.php
│   ├── SubscriptionService.php
│   └── MonitorService.php
├── Managers/
│   ├── ApiManager.php
│   ├── PolicyManager.php
│   ├── ProductManager.php
│   ├── SubscriptionManager.php
│   └── MonitorManager.php
├── Facades/
│   └── Apim.php
├── Exceptions/
│   ├── ApimException.php
│   └── ApimAuthException.php
├── ApimManager.php
├── ApimServiceProvider.php
└── LaravelAzureApimServiceProvider.php
config/
└── apim.php
```

---

## Error Handling

Azure API errors are mapped to `ApimException` with the HTTP status code and structured body:

```php
use Applab\LaravelAzureApim\Exceptions\ApimException;

try {
    Apim::api()->delete('non-existent-api');
} catch (ApimException $e) {
    $e->getHttpStatus();   // e.g. 404
    $e->getErrorBody();    // full Azure error payload
    $e->getMessage();      // human-readable message
}
```

Authentication failures throw `ApimAuthException`.

---

## Artisan Command

```bash
php artisan apim:info
```

Displays a quick summary of available facade methods and config publishing.

---

## Testing

```bash
composer test
```

---

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for recent changes.

## License

The MIT License (MIT). Please see [LICENSE](LICENSE.md) for more information.
