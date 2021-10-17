<?php

namespace XbNz\Resolver\Domain\Ip\Drivers;

use XbNz\Resolver\Domain\Ip\Actions\GetApiKeysForDriverAction;
use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Domain\Ip\DTOs\QueriedIpData;

class IpInfoDriver implements Driver
{
    private array $apiKeys;

    public function __construct(
        GetApiKeysForDriverAction $apiKeys
    )
    {
        $this->apiKeys = $apiKeys->execute($this);
    }

    public function query(IpData $ipData): QueriedIpData
    {
        //
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