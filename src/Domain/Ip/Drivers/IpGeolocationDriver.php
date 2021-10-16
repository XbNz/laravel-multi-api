<?php

namespace XbNz\Resolver\Domain\Ip\Drivers;

use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Domain\Ip\DTOs\QueriedIpData;

class IpGeolocationDriver implements Driver
{
    private string $apiKey;

    public function __construct(string $apiKey = null)
    {

    }

    public function query(IpData $ipData): QueriedIpData
    {

    }

    public function supports(): string
    {
        return 'ipGeolocation';
    }

    public function requiresApiKey(): bool
    {
        // TODO: Implement requiresApiKey() method.
    }

    public function requiresFile(): bool
    {
        // TODO: Implement requiresFile() method.
    }
}