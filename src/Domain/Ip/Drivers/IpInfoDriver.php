<?php

namespace XbNz\Resolver\Domain\Ip\Drivers;

use XbNz\Resolver\Domain\Ip\Actions\GetApiKeyForDriverAction;
use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Domain\Ip\DTOs\QueriedIpData;

class IpInfoDriver implements Driver
{
    private string $apiKey;

    public function __construct(
        GetApiKeyForDriverAction $apiKey
    )
    {
        $this->apiKey = $apiKey->execute($this);
    }

    public function query(IpData $ipData): QueriedIpData
    {

    }

    public function supports(): string
    {
        return 'ipInfo';
    }

    public function requiresApiKey(): bool
    {
        return true;
    }

    public function requiresFile(): bool
    {
        return false;
    }
}