<?php

namespace Applab\LaravelAzureApim\DTO;

class ThrottlePolicyData
{
    public function __construct(
        public readonly string $apiId,
        public readonly int $calls,
        public readonly int $renewalPeriod,
        public readonly string $counterKey = 'subscription',
    ) {}

    /**
     * Create an instance from an associative array.
     */
    public static function from(array $data): self
    {
        return new self(
            apiId:         $data['apiId'],
            calls:         (int) $data['calls'],
            renewalPeriod: (int) $data['renewalPeriod'],
            counterKey:    $data['counterKey'] ?? 'subscription',
        );
    }

    /**
     * Generate the Azure APIM throttling policy XML.
     */
    public function toXml(): string
    {
        return <<<XML
        <policies>
            <inbound>
                <rate-limit-by-key calls="{$this->calls}"
                                   renewal-period="{$this->renewalPeriod}"
                                   counter-key="@(context.Subscription?.Key ?? &quot;anonymous&quot;)" />
                <base />
            </inbound>
            <backend>
                <base />
            </backend>
            <outbound>
                <base />
            </outbound>
            <on-error>
                <base />
            </on-error>
        </policies>
        XML;
    }
}
