<?php

namespace Applab\LaravelAzureApim\Services;

use Applab\LaravelAzureApim\DTO\ThrottlePolicyData;
use Applab\LaravelAzureApim\Http\ApimHttpClient;

class PolicyService
{
    public function __construct(protected ApimHttpClient $client) {}

    /**
     * Apply a rate-limit (throttle) policy to an API.
     */
    public function applyThrottle(ThrottlePolicyData $data): array
    {
        return $this->client->putXml(
            "apis/{$data->apiId}/policies/policy",
            $data->toXml()
        );
    }

    /**
     * Apply a custom XML policy to an API.
     */
    public function applyCustomPolicy(string $apiId, string $xmlPolicy): array
    {
        return $this->client->putXml(
            "apis/{$apiId}/policies/policy",
            $xmlPolicy
        );
    }

    /**
     * Get the currently applied policy for an API.
     */
    public function getPolicy(string $apiId): array
    {
        return $this->client->get(
            "apis/{$apiId}/policies/policy",
            ['format' => 'xml']
        );
    }

    /**
     * Remove the policy from an API.
     */
    public function deletePolicy(string $apiId): array
    {
        return $this->client->delete("apis/{$apiId}/policies/policy");
    }
}
