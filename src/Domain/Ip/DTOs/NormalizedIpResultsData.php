<?php

namespace XbNz\Resolver\Domain\Ip\DTOs;

use XbNz\Resolver\Support\Drivers\Driver;

class NormalizedIpResultsData
{
    public function __construct(
        public readonly string $provider,
        public readonly string $ip,
        public readonly string $country,
        public readonly string $city,
        public readonly string $longitude,
        public readonly string $latitude,
    )
    {}
}