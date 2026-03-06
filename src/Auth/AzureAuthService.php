<?php

namespace Applab\LaravelAzureApim\Auth;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Applab\LaravelAzureApim\Exceptions\ApimAuthException;

class AzureAuthService
{
    protected string $tenantId;
    protected string $clientId;
    protected string $clientSecret;
    protected int $cacheTtl;

    public function __construct()
    {
        $this->validateConfig();

        $this->tenantId     = config('apim.tenant_id');
        $this->clientId     = config('apim.client_id');
        $this->clientSecret = config('apim.client_secret');
        $this->cacheTtl     = (int) config('apim.token_cache_ttl', 3500);
    }

    /**
     * Ensure all required config values are present before use.
     *
     * @throws ApimAuthException
     */
    protected function validateConfig(): void
    {
        $required = [
            'apim.tenant_id'     => 'AZURE_TENANT_ID',
            'apim.client_id'     => 'AZURE_CLIENT_ID',
            'apim.client_secret' => 'AZURE_CLIENT_SECRET',
        ];

        $missing = [];

        foreach ($required as $configKey => $envKey) {
            if (empty(config($configKey))) {
                $missing[] = "{$envKey} (config: {$configKey})";
            }
        }

        if (!empty($missing)) {
            throw new ApimAuthException(
                'Missing required Azure APIM configuration. Please set the following environment variables: '
                . implode(', ', $missing)
            );
        }
    }

    /**
     * Get a valid Bearer token, using the cache when possible.
     */
    public function getAccessToken(): string
    {
        $cacheKey = 'azure_apim_access_token_' . md5($this->tenantId . $this->clientId);

        return Cache::remember($cacheKey, $this->cacheTtl, function () {
            return $this->fetchAccessToken();
        });
    }

    /**
     * Perform OAuth2 client-credentials flow against Azure AD.
     */
    protected function fetchAccessToken(): string
    {
        $url = "https://login.microsoftonline.com/{$this->tenantId}/oauth2/v2.0/token";

        $response = Http::asForm()->post($url, [
            'grant_type'    => 'client_credentials',
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'scope'         => 'https://management.azure.com/.default',
        ]);

        if ($response->failed()) {
            throw new ApimAuthException(
                'Failed to obtain Azure access token: ' . $response->body(),
                $response->status()
            );
        }

        $token = $response->json('access_token');

        if (empty($token)) {
            throw new ApimAuthException('Azure access token is empty in response.');
        }

        return $token;
    }
}
