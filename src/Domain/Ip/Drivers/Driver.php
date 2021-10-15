<?php

namespace XbNz\Resolver\Domain\Ip\Drivers;

use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Domain\Ip\DTOs\QueriedIpData;

interface Driver
{
    public function query(IpData $ipData): QueriedIpData;
    public function supports(): string;
    public function requiresApiKey(): bool;
    public function requiresFile(): bool;
}