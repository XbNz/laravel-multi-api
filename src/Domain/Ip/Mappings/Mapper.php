<?php

namespace XbNz\Resolver\Domain\Ip\Mappings;

use XbNz\Resolver\Domain\Ip\DTOs\NormalizedGeolocationResultsData;
use XbNz\Resolver\Domain\Ip\DTOs\RawIpResultsData;

interface Mapper
{
    public function map(RawIpResultsData $rawIpResults): NormalizedGeolocationResultsData;
    public function supports(string $driver): bool;
}