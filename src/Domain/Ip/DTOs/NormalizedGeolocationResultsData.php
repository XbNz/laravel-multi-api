<?php

declare(strict_types=1);

namespace XbNz\Resolver\Domain\Ip\DTOs;

use XbNz\Resolver\Support\DTOs\MappableDTO;

class NormalizedGeolocationResultsData implements MappableDTO
{
    public function __construct(
        public readonly string $provider,
        public readonly string $ip,
        public readonly ?string $country = null,
        public readonly ?string $city = null,
        public readonly ?float $latitude = null,
        public readonly ?float $longitude = null,
        public readonly ?string $organization = null,
    ) {
    }
}
