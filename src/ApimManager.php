<?php

namespace Applab\LaravelAzureApim;

use Applab\LaravelAzureApim\Managers\ApiManager;
use Applab\LaravelAzureApim\Managers\MonitorManager;
use Applab\LaravelAzureApim\Managers\PolicyManager;
use Applab\LaravelAzureApim\Managers\ProductManager;
use Applab\LaravelAzureApim\Managers\SubscriptionManager;

/**
 * Main entry point exposed via the Apim facade.
 *
 * Usage:
 *   Apim::api()->create(...)
 *   Apim::policy()->applyThrottle(...)
 *   Apim::product()->create(...)
 *   Apim::subscription()->subscribe(...)
 *   Apim::monitor()->apiUsage(...)
 */
class ApimManager
{
    public function __construct(
        protected ApiManager          $apiManager,
        protected PolicyManager       $policyManager,
        protected ProductManager      $productManager,
        protected SubscriptionManager $subscriptionManager,
        protected MonitorManager      $monitorManager,
    ) {}

    /**
     * Access API management operations.
     */
    public function api(): ApiManager
    {
        return $this->apiManager;
    }

    /**
     * Access policy management operations.
     */
    public function policy(): PolicyManager
    {
        return $this->policyManager;
    }

    /**
     * Access product management operations.
     */
    public function product(): ProductManager
    {
        return $this->productManager;
    }

    /**
     * Access subscription management operations.
     */
    public function subscription(): SubscriptionManager
    {
        return $this->subscriptionManager;
    }

    /**
     * Access monitoring & analytics operations.
     */
    public function monitor(): MonitorManager
    {
        return $this->monitorManager;
    }
}
