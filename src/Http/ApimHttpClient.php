<?php

namespace Applab\LaravelAzureApim\Http;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Applab\LaravelAzureApim\Auth\AzureAuthService;
use Applab\LaravelAzureApim\Exceptions\ApimAuthException;
use Applab\LaravelAzureApim\Exceptions\ApimException;

class ApimHttpClient
{
    protected readonly string $baseUrl;
    protected readonly string $apiVersion;
    protected readonly int $timeout;
    protected readonly int $retryTimes;
    protected readonly int $retrySleepMs;

    public function __construct(protected readonly AzureAuthService $auth)
    {
        $this->validateConfig();

        $subscriptionId = config('apim.subscription_id');
        $resourceGroup  = config('apim.resource_group');
        $serviceName    = config('apim.service_name');

        $this->baseUrl = sprintf(
            'https://management.azure.com/subscriptions/%s/resourceGroups/%s/providers/Microsoft.ApiManagement/service/%s',
            $subscriptionId,
            $resourceGroup,
            $serviceName,
        );

        $this->apiVersion   = config('apim.api_version', '2022-08-01');
        $this->timeout      = (int) config('apim.timeout', 30);
        $this->retryTimes   = (int) config('apim.retry_times', 3);
        $this->retrySleepMs = (int) config('apim.retry_sleep_ms', 500);
    }

    // ─── Public HTTP helpers ──────────────────────────────────────────────────

    public function get(string $path, array $query = []): array
    {
        return $this->send('GET', $path, $query);
    }

    public function put(string $path, array $body = [], array $query = []): array
    {
        return $this->send('PUT', $path, $query, $body);
    }

    public function patch(string $path, array $body = [], array $query = []): array
    {
        return $this->send('PATCH', $path, $query, $body);
    }

    public function delete(string $path, array $query = []): array
    {
        return $this->send('DELETE', $path, $query);
    }

    /**
     * Send a PUT request with a raw XML body (used for policies).
     */
    public function putXml(string $path, string $xmlBody, array $query = []): array
    {
        $url = $this->buildUrl($path, $query);

        $response = $this->buildClient()
            ->contentType('application/vnd.ms-azure-apim.policy+xml')
            ->withBody($xmlBody, 'application/vnd.ms-azure-apim.policy+xml')
            ->put($url);

        return $this->handleResponse($response);
    }

    // ─── Core dispatcher ──────────────────────────────────────────────────────

    /**
     * Dispatch any standard JSON request via a single Http::send() call.
     *
     * @throws ApimException|ConnectionException
     */
    protected function send(string $method, string $path, array $query = [], array $body = []): array
    {
        $url = $this->buildUrl($path, $query);

        try {
            $response = $this->buildClient()->send(strtoupper($method), $url, [
                'json' => blank($body) ? null : $body,
            ]);
        } catch (ConnectionException $e) {
            throw new ApimException(
                "Connection to Azure APIM failed: {$e->getMessage()}",
                503,
                [],
                $e,
            );
        }

        return $this->handleResponse($response);
    }

    // ─── Shared client builder ────────────────────────────────────────────────

    /**
     * Build a pre-configured PendingRequest with auth, retry logic, and timeouts.
     */
    protected function buildClient(): PendingRequest
    {
        return Http::withToken($this->auth->getAccessToken())
            ->acceptJson()
            ->timeout($this->timeout)
            ->retry(
                times:   $this->retryTimes,
                sleepMilliseconds: $this->retrySleepMs,
                when: fn (mixed $e, Response $r) => $r->status() === 429,
                throw: false,
            );
    }

    // ─── URL builder ─────────────────────────────────────────────────────────

    protected function buildUrl(string $path, array $query = []): string
    {
        $query['api-version'] = $this->apiVersion;

        return rtrim($this->baseUrl, '/')
            . '/' . ltrim($path, '/')
            . '?' . http_build_query($query);
    }

    // ─── Response handler ────────────────────────────────────────────────────

    /**
     * Return decoded body on success, or throw a rich ApimException on failure.
     *
     * @throws ApimException
     */
    protected function handleResponse(Response $response): array
    {
        if ($response->successful()) {
            return $response->json() ?? [];
        }

        $status    = $response->status();
        $body      = $response->json() ?? [];
        $message   = filled($body['error']['message'] ?? null)
            ? $body['error']['message']
            : $response->body();
        $requestId = $response->header('x-ms-request-id');

        $detail = filled($requestId) ? " [request-id: {$requestId}]" : '';

        $exceptionMessage = match ($status) {
            400 => "Bad Request: {$message}{$detail}",
            401 => "Unauthorized: {$message}{$detail}",
            403 => "Forbidden: {$message}{$detail}",
            404 => "Not Found: {$message}{$detail}",
            409 => "Conflict: {$message}{$detail}",
            422 => "Unprocessable Entity: {$message}{$detail}",
            429 => "Too Many Requests – rate limit hit: {$message}{$detail}",
            500 => "Azure APIM internal error: {$message}{$detail}",
            default => "Azure APIM API error [{$status}]: {$message}{$detail}",
        };

        throw new ApimException($exceptionMessage, $status, $body);
    }

    // ─── Config validation ────────────────────────────────────────────────────

    /**
     * Guard against missing resource-level config values.
     *
     * @throws ApimAuthException
     */
    protected function validateConfig(): void
    {
        $required = [
            'apim.subscription_id' => 'AZURE_SUBSCRIPTION_ID',
            'apim.resource_group'  => 'AZURE_RESOURCE_GROUP',
            'apim.service_name'    => 'AZURE_APIM_SERVICE_NAME',
        ];

        $missing = array_keys(array_filter(
            $required,
            fn (string $env, string $key) => blank(config($key)),
            ARRAY_FILTER_USE_BOTH,
        ));

        if (!empty($missing)) {
            $list = implode(', ', array_map(
                fn (string $key) => "{$required[$key]} (config: {$key})",
                $missing,
            ));

            throw new ApimAuthException(
                "Missing required Azure APIM configuration. Please set: {$list}"
            );
        }
    }
}

