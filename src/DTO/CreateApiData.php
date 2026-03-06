<?php

namespace Applab\LaravelAzureApim\DTO;

class CreateApiData
{
    public function __construct(
        public readonly string $apiId,
        public readonly string $openApiSpecification,
        public readonly string $path,
        public readonly string $backendUrl,
        public readonly string $displayName = '',
        public readonly string $format = 'openapi+json',
    ) {}

    /**
     * Create an instance from an associative array.
     */
    public static function from(array $data): self
    {
        return new self(
            apiId:                $data['apiId'],
            openApiSpecification: $data['openApiSpecification'],
            path:                 $data['path'],
            backendUrl:           $data['backendUrl'],
            displayName:          $data['displayName'] ?? $data['apiId'],
            format:               $data['format'] ?? 'openapi+json',
        );
    }

    /**
     * Convert to Azure APIM REST API body.
     */
    public function toArray(): array
    {
        return [
            'properties' => [
                'displayName'        => $this->displayName ?: $this->apiId,
                'path'               => $this->path,
                'format'             => $this->format,
                'value'              => $this->openApiSpecification,
                'serviceUrl'         => $this->backendUrl,
                'protocols'          => ['https'],
                'subscriptionRequired' => true,
            ],
        ];
    }
}
