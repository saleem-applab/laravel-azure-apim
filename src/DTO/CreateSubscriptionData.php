<?php

namespace Applab\LaravelAzureApim\DTO;

class CreateSubscriptionData
{
    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $productId,
        public readonly string $userId,
        public readonly string $displayName = '',
        public readonly string $state = 'active',
    ) {}

    /**
     * Create an instance from an associative array.
     */
    public static function from(array $data): self
    {
        return new self(
            subscriptionId: $data['subscriptionId'],
            productId:      $data['productId'],
            userId:         $data['userId'],
            displayName:    $data['displayName'] ?? $data['subscriptionId'],
            state:          $data['state'] ?? 'active',
        );
    }

    /**
     * Convert to Azure APIM REST API body.
     */
    public function toArray(): array
    {
        return [
            'properties' => [
                'scope'       => "/products/{$this->productId}",
                'ownerId'     => "/users/{$this->userId}",
                'displayName' => $this->displayName ?: $this->subscriptionId,
                'state'       => $this->state,
            ],
        ];
    }
}
