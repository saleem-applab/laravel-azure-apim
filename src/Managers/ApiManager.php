<?php

namespace Applab\LaravelAzureApim\Managers;

use Applab\LaravelAzureApim\DTO\CreateApiData;
use Applab\LaravelAzureApim\DTO\UpdateApiData;
use Applab\LaravelAzureApim\Services\ApiService;

class ApiManager
{
    public function __construct(protected ApiService $service) {}

    /**
     * Create a new API in APIM.
     */
    public function create(CreateApiData $data): array
    {
        return $this->service->create($data);
    }

    /**
     * Update an existing API.
     */
    public function update(UpdateApiData $data): array
    {
        return $this->service->update($data);
    }

    /**
     * Delete an API by its ID.
     */
    public function delete(string $apiId): array
    {
        return $this->service->delete($apiId);
    }

    /**
     * List all APIs.
     */
    public function list(): array
    {
        return $this->service->list();
    }
}
