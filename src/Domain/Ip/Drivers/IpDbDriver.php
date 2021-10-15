<?php

namespace XbNz\Resolver\Domain\Ip\Drivers;

use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Domain\Ip\DTOs\QueriedIpData;

class IpDbDriver implements Driver
{

    public function __construct(

    )
    {
    }

    public function query(IpData $ipData): QueriedIpData
    {

    }

    public function supports(): string
    {
        return 'ipDb';
    }

    public function requiresApiKey(): bool
    {

    }

    public function requiresFile(): bool
    {

    }
}