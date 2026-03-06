<?php

namespace Applab\LaravelAzureApim\DTO;

class AssignApiToProductData
{
    public function __construct(
        public readonly string $productId,
        public readonly string $apiId,
    ) {}

    /**
     * Create an instance from an associative array.
     */
    public static function from(array $data): self
    {
        return new self(
            productId: $data['productId'],
            apiId:     $data['apiId'],
        );
    }
}
