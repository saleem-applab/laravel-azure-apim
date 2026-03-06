<?php

namespace Applab\LaravelAzureApim\Managers;

use Applab\LaravelAzureApim\DTO\ThrottlePolicyData;
use Applab\LaravelAzureApim\Services\PolicyService;

class PolicyManager
{
    public function __construct(protected PolicyService $service) {}

    /**
     * Apply a throttle (rate-limit) policy to an API.
     */
    public function applyThrottle(ThrottlePolicyData $data): array
    {
        return $this->service->applyThrottle($data);
    }

    /**
     * Alias for applyThrottle – matches the facade documentation.
     */
    public function throttle(ThrottlePolicyData $data): array
    {
        return $this->applyThrottle($data);
    }

    /**
     * Apply a custom XML policy to an API.
     */
    public function applyCustomPolicy(string $apiId, string $xmlPolicy): array
    {
        return $this->service->applyCustomPolicy($apiId, $xmlPolicy);
    }

    /**
     * Get the currently applied policy for an API.
     */
    public function getPolicy(string $apiId): array
    {
        return $this->service->getPolicy($apiId);
    }

    /**
     * Remove the policy from an API.
     */
    public function deletePolicy(string $apiId): array
    {
        return $this->service->deletePolicy($apiId);
    }
}
