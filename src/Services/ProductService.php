<?php

namespace Applab\LaravelAzureApim\Services;

use Applab\LaravelAzureApim\DTO\AssignApiToProductData;
use Applab\LaravelAzureApim\DTO\CreateProductData;
use Applab\LaravelAzureApim\Http\ApimHttpClient;

class ProductService
{
    public function __construct(protected ApimHttpClient $client) {}

    /**
     * Create or replace a product.
     */
    public function create(CreateProductData $data): array
    {
        return $this->client->put(
            "products/{$data->productId}",
            $data->toArray()
        );
    }

    /**
     * Update an existing product.
     */
    public function update(CreateProductData $data): array
    {
        return $this->client->patch(
            "products/{$data->productId}",
            $data->toArray()
        );
    }

    /**
     * Delete a product by ID.
     */
    public function delete(string $productId): array
    {
        return $this->client->delete(
            "products/{$productId}",
            ['deleteSubscriptions' => 'true']
        );
    }

    /**
     * Assign an API to a product.
     */
    public function assignApi(AssignApiToProductData $data): array
    {
        return $this->client->put(
            "products/{$data->productId}/apis/{$data->apiId}",
            []
        );
    }

    /**
     * List all products.
     */
    public function list(): array
    {
        return $this->client->get('products');
    }
}
