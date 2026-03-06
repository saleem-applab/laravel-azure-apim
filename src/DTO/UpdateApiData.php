<?php

namespace Applab\LaravelAzureApim\DTO;

class UpdateApiData
{
    public function __construct(
        public readonly string $apiId,
        public readonly ?string $backendUrl = null,
        public readonly ?string $displayName = null,
        public readonly ?string $path = null,
    ) {}

    /**
     * Create an instance from an associative array.
     */
    public static function from(array $data): self
    {
        return new self(
            apiId:       $data['apiId'],
            backendUrl:  $data['backendUrl'] ?? null,
            displayName: $data['displayName'] ?? null,
            path:        $data['path'] ?? null,
        );
    }

    /**
     * Convert to Azure APIM REST API body (PATCH).
     */
    public function toArray(): array
    {
        $properties = array_filter([
            'serviceUrl'  => $this->backendUrl,
            'displayName' => $this->displayName,
            'path'        => $this->path,
        ], fn ($v) => $v !== null);

        return ['properties' => $properties];
    }
}
