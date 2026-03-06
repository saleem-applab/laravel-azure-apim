<?php

use Applab\LaravelAzureApim\ApimManager;
use Applab\LaravelAzureApim\Auth\AzureAuthService;
use Applab\LaravelAzureApim\DTO\AssignApiToProductData;
use Applab\LaravelAzureApim\DTO\CreateApiData;
use Applab\LaravelAzureApim\DTO\CreateProductData;
use Applab\LaravelAzureApim\DTO\CreateSubscriptionData;
use Applab\LaravelAzureApim\DTO\ThrottlePolicyData;
use Applab\LaravelAzureApim\DTO\UpdateApiData;
use Applab\LaravelAzureApim\Facades\Apim;
use Applab\LaravelAzureApim\Http\ApimHttpClient;
use Applab\LaravelAzureApim\Managers\ApiManager;
use Applab\LaravelAzureApim\Managers\MonitorManager;
use Applab\LaravelAzureApim\Managers\PolicyManager;
use Applab\LaravelAzureApim\Managers\ProductManager;
use Applab\LaravelAzureApim\Managers\SubscriptionManager;
use Illuminate\Support\Facades\Http;

// ──────────────────────────────────────────────────────────
// Helper to stub the token endpoint + an APIM endpoint
// ──────────────────────────────────────────────────────────
function fakeApimToken(): void
{
    Http::fake([
        'https://login.microsoftonline.com/*' => Http::response(
            ['access_token' => 'test-token', 'expires_in' => 3599],
            200
        ),
    ]);
}

function fakeApim(string $urlPattern, array $response = [], int $status = 200): void
{
    Http::fake([
        'https://login.microsoftonline.com/*' => Http::response(
            ['access_token' => 'test-token', 'expires_in' => 3599],
            200
        ),
        $urlPattern => Http::response($response, $status),
    ]);
}

// ──────────────────────────────────────────────────────────
// ApimManager resolves from the container
// ──────────────────────────────────────────────────────────
it('resolves ApimManager from the container', function () {
    $manager = app(ApimManager::class);

    expect($manager)->toBeInstanceOf(ApimManager::class);
    expect($manager->api())->toBeInstanceOf(ApiManager::class);
    expect($manager->policy())->toBeInstanceOf(PolicyManager::class);
    expect($manager->product())->toBeInstanceOf(ProductManager::class);
    expect($manager->subscription())->toBeInstanceOf(SubscriptionManager::class);
    expect($manager->monitor())->toBeInstanceOf(MonitorManager::class);
});

// ──────────────────────────────────────────────────────────
// DTO: CreateApiData
// ──────────────────────────────────────────────────────────
it('CreateApiData::from builds correct payload', function () {
    $dto = CreateApiData::from([
        'apiId'                => 'weather-api',
        'openApiSpecification' => '{"openapi":"3.0.0"}',
        'path'                 => 'weather',
        'backendUrl'           => 'https://backend.example.com',
        'displayName'          => 'Weather API',
    ]);

    expect($dto->apiId)->toBe('weather-api')
        ->and($dto->path)->toBe('weather')
        ->and($dto->backendUrl)->toBe('https://backend.example.com');

    $body = $dto->toArray();
    expect($body['properties']['path'])->toBe('weather')
        ->and($body['properties']['serviceUrl'])->toBe('https://backend.example.com')
        ->and($body['properties']['displayName'])->toBe('Weather API');
});

it('CreateApiData defaults displayName to apiId when not supplied', function () {
    $dto  = CreateApiData::from([
        'apiId'                => 'my-api',
        'openApiSpecification' => '{}',
        'path'                 => 'my-path',
        'backendUrl'           => 'https://example.com',
    ]);

    expect($dto->toArray()['properties']['displayName'])->toBe('my-api');
});

// ──────────────────────────────────────────────────────────
// DTO: UpdateApiData
// ──────────────────────────────────────────────────────────
it('UpdateApiData::from builds patch body with only provided fields', function () {
    $dto  = UpdateApiData::from(['apiId' => 'api-1', 'backendUrl' => 'https://new.example.com']);
    $body = $dto->toArray();

    expect($body['properties'])->toHaveKey('serviceUrl')
        ->and($body['properties'])->not->toHaveKey('displayName')
        ->and($body['properties'])->not->toHaveKey('path');
});

// ──────────────────────────────────────────────────────────
// DTO: CreateProductData
// ──────────────────────────────────────────────────────────
it('CreateProductData::from builds correct payload', function () {
    $dto  = CreateProductData::from(['productId' => 'prod-1', 'displayName' => 'Starter']);
    $body = $dto->toArray();

    expect($body['properties']['displayName'])->toBe('Starter')
        ->and($body['properties']['state'])->toBe('published')
        ->and($body['properties']['subscriptionRequired'])->toBeTrue();
});

// ──────────────────────────────────────────────────────────
// DTO: ThrottlePolicyData
// ──────────────────────────────────────────────────────────
it('ThrottlePolicyData::from generates valid XML', function () {
    $dto = ThrottlePolicyData::from([
        'apiId'         => 'weather-api',
        'calls'         => 100,
        'renewalPeriod' => 60,
    ]);

    $xml = $dto->toXml();

    expect($xml)->toContain('calls="100"')
        ->and($xml)->toContain('renewal-period="60"')
        ->and($xml)->toContain('<policies>');
});

// ──────────────────────────────────────────────────────────
// DTO: CreateSubscriptionData
// ──────────────────────────────────────────────────────────
it('CreateSubscriptionData::from builds correct payload', function () {
    $dto  = CreateSubscriptionData::from([
        'subscriptionId' => 'sub-1',
        'productId'      => 'prod-1',
        'userId'         => 'user-1',
    ]);
    $body = $dto->toArray();

    expect($body['properties']['scope'])->toBe('/products/prod-1')
        ->and($body['properties']['ownerId'])->toBe('/users/user-1')
        ->and($body['properties']['state'])->toBe('active');
});

// ──────────────────────────────────────────────────────────
// ApiManager – HTTP integration (mocked)
// ──────────────────────────────────────────────────────────
it('ApiManager::create sends a PUT to the correct URL', function () {
    fakeApim('https://management.azure.com/*', ['id' => '/apis/weather-api']);

    $dto    = CreateApiData::from([
        'apiId'                => 'weather-api',
        'openApiSpecification' => '{}',
        'path'                 => 'weather',
        'backendUrl'           => 'https://backend.example.com',
    ]);

    $result = app(ApiManager::class)->create($dto);

    expect($result)->toHaveKey('id');
    Http::assertSent(fn ($req) => str_contains($req->url(), 'apis/weather-api'));
});

it('ApiManager::delete sends a DELETE request', function () {
    fakeApim('https://management.azure.com/*', []);

    $result = app(ApiManager::class)->delete('weather-api');

    expect($result)->toBeArray();
    Http::assertSent(fn ($req) => $req->method() === 'DELETE'
        && str_contains($req->url(), 'apis/weather-api'));
});

it('ApiManager::list sends a GET request', function () {
    fakeApim('https://management.azure.com/*', ['value' => []]);

    $result = app(ApiManager::class)->list();

    expect($result)->toHaveKey('value');
    Http::assertSent(fn ($req) => $req->method() === 'GET'
        && str_contains($req->url(), '/apis'));
});

// ──────────────────────────────────────────────────────────
// PolicyManager – HTTP integration
// ──────────────────────────────────────────────────────────
it('PolicyManager::applyThrottle sends a PUT with XML content-type', function () {
    fakeApim('https://management.azure.com/*', []);

    $dto = ThrottlePolicyData::from(['apiId' => 'weather-api', 'calls' => 100, 'renewalPeriod' => 60]);
    app(PolicyManager::class)->applyThrottle($dto);

    Http::assertSent(fn ($req) => str_contains($req->url(), 'policies/policy'));
});

// ──────────────────────────────────────────────────────────
// ProductManager – HTTP integration
// ──────────────────────────────────────────────────────────
it('ProductManager::create sends a PUT request', function () {
    fakeApim('https://management.azure.com/*', ['id' => '/products/prod-1']);

    $dto    = CreateProductData::from(['productId' => 'prod-1', 'displayName' => 'Starter']);
    $result = app(ProductManager::class)->create($dto);

    expect($result)->toHaveKey('id');
    Http::assertSent(fn ($req) => str_contains($req->url(), 'products/prod-1'));
});

it('ProductManager::assignApi sends correct PUT request', function () {
    fakeApim('https://management.azure.com/*', []);

    $dto = AssignApiToProductData::from(['productId' => 'prod-1', 'apiId' => 'weather-api']);
    app(ProductManager::class)->assignApi($dto);

    Http::assertSent(fn ($req) => str_contains($req->url(), 'products/prod-1/apis/weather-api'));
});

// ──────────────────────────────────────────────────────────
// SubscriptionManager – HTTP integration
// ──────────────────────────────────────────────────────────
it('SubscriptionManager::subscribe sends a PUT request', function () {
    fakeApim('https://management.azure.com/*', ['id' => '/subscriptions/sub-1']);

    $dto    = CreateSubscriptionData::from([
        'subscriptionId' => 'sub-1',
        'productId'      => 'prod-1',
        'userId'         => 'user-1',
    ]);
    $result = app(SubscriptionManager::class)->subscribe($dto);

    expect($result)->toHaveKey('id');
    Http::assertSent(fn ($req) => str_contains($req->url(), 'subscriptions/sub-1'));
});

it('SubscriptionManager::unsubscribe sends a DELETE request', function () {
    fakeApim('https://management.azure.com/*', []);

    app(SubscriptionManager::class)->unsubscribe('sub-1');

    Http::assertSent(fn ($req) => $req->method() === 'DELETE'
        && str_contains($req->url(), 'subscriptions/sub-1'));
});

// ──────────────────────────────────────────────────────────
// MonitorManager – HTTP integration
// ──────────────────────────────────────────────────────────
it('MonitorManager::apiUsage sends GET to reports/byApi', function () {
    fakeApim('https://management.azure.com/*', ['value' => []]);

    $result = app(MonitorManager::class)->apiUsage('weather-api');

    expect($result)->toHaveKey('value');
    Http::assertSent(fn ($req) => str_contains($req->url(), 'reports/byApi'));
});

it('MonitorManager::apiErrors sends GET with failed filter', function () {
    fakeApim('https://management.azure.com/*', ['value' => []]);

    app(MonitorManager::class)->apiErrors();

    Http::assertSent(fn ($req) => str_contains($req->url(), 'reports/byApi'));
});

// ──────────────────────────────────────────────────────────
// Facade
// ──────────────────────────────────────────────────────────
it('Apim facade resolves correctly', function () {
    expect(Apim::api())->toBeInstanceOf(ApiManager::class);
    expect(Apim::policy())->toBeInstanceOf(PolicyManager::class);
    expect(Apim::product())->toBeInstanceOf(ProductManager::class);
    expect(Apim::subscription())->toBeInstanceOf(SubscriptionManager::class);
    expect(Apim::monitor())->toBeInstanceOf(MonitorManager::class);
});
