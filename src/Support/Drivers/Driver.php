<?php

namespace XbNz\Resolver\Support\Drivers;

use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Domain\Ip\DTOs\QueriedIpData;

interface Driver
{
    public function query(IpData $ipData): QueriedIpData;
    public function raw(IpData $ipData): array;
    public function supports(): string;
    public function requiresApiKey(): bool;
    public function requiresFile(): bool;
}