<?php

namespace Applab\LaravelAzureApim\Services;

use Applab\LaravelAzureApim\DTO\CreateApiData;
use Applab\LaravelAzureApim\DTO\UpdateApiData;
use Applab\LaravelAzureApim\Http\ApimHttpClient;

class ApiService
{
    public function __construct(protected ApimHttpClient $client) {}

    /**
     * Create or fully replace an API.
     */
    public function create(CreateApiData $data): array
    {
        return $this->client->put(
            "apis/{$data->apiId}",
            $data->toArray()
        );
    }

    /**
     * Update an existing API (partial update via PATCH).
     */
    public function update(UpdateApiData $data): array
    {
        return $this->client->patch(
            "apis/{$data->apiId}",
            $data->toArray()
        );
    }

    /**
     * Delete an API by ID.
     */
    public function delete(string $apiId): array
    {
        return $this->client->delete(
            "apis/{$apiId}",
            ['deleteRevisions' => 'true']
        );
    }

    /**
     * List all APIs in the APIM service.
     */
    public function list(): array
    {
        return $this->client->get('apis');
    }
}
