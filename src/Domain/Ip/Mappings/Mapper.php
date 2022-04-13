<?php

namespace XbNz\Resolver\Domain\Ip\Mappings;

use XbNz\Resolver\Domain\Ip\DTOs\MappableDTO;
use XbNz\Resolver\Domain\Ip\DTOs\NormalizedGeolocationResultsData;
use XbNz\Resolver\Domain\Ip\DTOs\RawIpResultsData;

interface Mapper
{
    public function map(RawIpResultsData $rawIpResults): MappableDTO;
    public function supports(string $driver): bool;
}