<?php

namespace XbNz\Resolver\Domain\Ip\Strategies\SoloIpAddressStrategies;

use XbNz\Resolver\Domain\Ip\DTOs\IpData;

interface SoloIpStrategy
{
    public function guzzleMiddleware(IpData $ipData): callable;
    public function supports(string $apiBaseUri): bool;
}