<?php

namespace Applab\LaravelAzureApim\Managers;

use Applab\LaravelAzureApim\DTO\CreateSubscriptionData;
use Applab\LaravelAzureApim\Services\SubscriptionService;

class SubscriptionManager
{
    public function __construct(protected SubscriptionService $service) {}

    /**
     * Subscribe a user to a product.
     */
    public function subscribe(CreateSubscriptionData $data): array
    {
        return $this->service->subscribe($data);
    }

    /**
     * Unsubscribe / cancel a subscription.
     */
    public function unsubscribe(string $subscriptionId): array
    {
        return $this->service->unsubscribe($subscriptionId);
    }

    /**
     * List all subscriptions.
     */
    public function list(): array
    {
        return $this->service->list();
    }
}
