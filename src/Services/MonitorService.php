<?php

namespace Applab\LaravelAzureApim\Services;

use Applab\LaravelAzureApim\Http\ApimHttpClient;

class MonitorService
{
    public function __construct(protected ApimHttpClient $client) {}

    /**
     * Get usage statistics for a specific API.
     *
     * Uses the Azure APIM Reports by API endpoint.
     */
    public function apiUsage(string $apiId): array
    {
        return $this->client->get('reports/byApi', [
            '$filter' => "apiId eq '{$apiId}'",
        ]);
    }

    /**
     * Get usage statistics for a specific product.
     */
    public function productUsage(string $productId): array
    {
        return $this->client->get('reports/byProduct', [
            '$filter' => "productId eq '{$productId}'",
        ]);
    }

    /**
     * Get error statistics across all APIs.
     */
    public function apiErrors(): array
    {
        return $this->client->get('reports/byApi', [
            '$filter' => 'callCountFailed gt 0',
            '$orderby' => 'callCountFailed desc',
        ]);
    }

    /**
     * Get request statistics by subscription.
     */
    public function bySubscription(): array
    {
        return $this->client->get('reports/bySubscription');
    }

    /**
     * Get request statistics by time interval.
     *
     * @param string $interval  ISO 8601 duration (e.g. 'PT1H')
     */
    public function byTime(string $interval = 'PT1H'): array
    {
        return $this->client->get('reports/byTime', ['interval' => $interval]);
    }
}
