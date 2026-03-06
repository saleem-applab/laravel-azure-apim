<?php

namespace Applab\LaravelAzureApim\Managers;

use Applab\LaravelAzureApim\Services\MonitorService;

class MonitorManager
{
    public function __construct(protected MonitorService $service) {}

    /**
     * Get usage statistics for a specific API.
     */
    public function apiUsage(string $apiId): array
    {
        return $this->service->apiUsage($apiId);
    }

    /**
     * Get usage statistics for a specific product.
     */
    public function productUsage(string $productId): array
    {
        return $this->service->productUsage($productId);
    }

    /**
     * Get error statistics across all APIs.
     */
    public function apiErrors(): array
    {
        return $this->service->apiErrors();
    }

    /**
     * Get request statistics by subscription.
     */
    public function bySubscription(): array
    {
        return $this->service->bySubscription();
    }

    /**
     * Get request statistics by time interval.
     */
    public function byTime(string $interval = 'PT1H'): array
    {
        return $this->service->byTime($interval);
    }
}
