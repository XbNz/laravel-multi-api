<?php

namespace XbNz\Resolver\Domain\Ip\Mappings;

use XbNz\Resolver\Domain\Ip\DTOs\NormalizedIpResultsData;
use XbNz\Resolver\Domain\Ip\DTOs\RawIpResultsData;

interface Mapper
{
    public function map(RawIpResultsData $rawIpResults): NormalizedIpResultsData;
    public function supports(string $driver): bool;
}