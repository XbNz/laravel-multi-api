<?php

namespace XbNz\Resolver\Support\Mappings;

use XbNz\Resolver\Support\DTOs\MappableDTO;
use XbNz\Resolver\Support\DTOs\RawResultsData;

interface Mapper
{
    public function map(RawResultsData $rawIpResults): MappableDTO;
    public function supports(string $driver): bool;
}