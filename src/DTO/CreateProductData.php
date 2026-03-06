<?php

namespace Applab\LaravelAzureApim\DTO;

class CreateProductData
{
    public function __construct(
        public readonly string $productId,
        public readonly string $displayName,
        public readonly string $description = '',
        public readonly string $state = 'published',
        public readonly bool $subscriptionRequired = true,
    ) {}

    /**
     * Create an instance from an associative array.
     */
    public static function from(array $data): self
    {
        return new self(
            productId:            $data['productId'],
            displayName:          $data['displayName'],
            description:          $data['description'] ?? '',
            state:                $data['state'] ?? 'published',
            subscriptionRequired: $data['subscriptionRequired'] ?? true,
        );
    }

    /**
     * Convert to Azure APIM REST API body.
     */
    public function toArray(): array
    {
        return [
            'properties' => [
                'displayName'          => $this->displayName,
                'description'          => $this->description,
                'state'                => $this->state,
                'subscriptionRequired' => $this->subscriptionRequired,
            ],
        ];
    }
}
