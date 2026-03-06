<?php

namespace Applab\LaravelAzureApim\Managers;

use Applab\LaravelAzureApim\DTO\AssignApiToProductData;
use Applab\LaravelAzureApim\DTO\CreateProductData;
use Applab\LaravelAzureApim\Services\ProductService;

class ProductManager
{
    public function __construct(protected ProductService $service) {}

    /**
     * Create a new product.
     */
    public function create(CreateProductData $data): array
    {
        return $this->service->create($data);
    }

    /**
     * Update an existing product.
     */
    public function update(CreateProductData $data): array
    {
        return $this->service->update($data);
    }

    /**
     * Delete a product by its ID.
     */
    public function delete(string $productId): array
    {
        return $this->service->delete($productId);
    }

    /**
     * Assign an API to a product.
     */
    public function assignApi(AssignApiToProductData $data): array
    {
        return $this->service->assignApi($data);
    }

    /**
     * List all products.
     */
    public function list(): array
    {
        return $this->service->list();
    }
}
