<?php

namespace Applab\LaravelAzureApim\Services;

use Applab\LaravelAzureApim\DTO\CreateSubscriptionData;
use Applab\LaravelAzureApim\Http\ApimHttpClient;

class SubscriptionService
{
    public function __construct(protected ApimHttpClient $client) {}

    /**
     * Create or replace a subscription.
     */
    public function subscribe(CreateSubscriptionData $data): array
    {
        return $this->client->put(
            "subscriptions/{$data->subscriptionId}",
            $data->toArray()
        );
    }

    /**
     * Cancel / delete a subscription by ID.
     */
    public function unsubscribe(string $subscriptionId): array
    {
        return $this->client->delete("subscriptions/{$subscriptionId}");
    }

    /**
     * List all subscriptions.
     */
    public function list(): array
    {
        return $this->client->get('subscriptions');
    }
}
