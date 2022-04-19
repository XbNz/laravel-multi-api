<?php

declare(strict_types=1);

namespace XbNz\Resolver\Domain\Ip\Mappings;

use XbNz\Resolver\Domain\Ip\Drivers\IpApiDotCoDriver;
use XbNz\Resolver\Domain\Ip\DTOs\NormalizedGeolocationResultsData;
use XbNz\Resolver\Support\DTOs\MappableDTO;
use XbNz\Resolver\Support\DTOs\RawResultsData;
use XbNz\Resolver\Support\Mappings\Mapper;

class IpApiDotCoMapper implements Mapper
{
    public function map(RawResultsData $rawIpResults): MappableDTO
    {
        return new NormalizedGeolocationResultsData(
            $rawIpResults->provider,
            $rawIpResults->data['ip'],
            optional($rawIpResults->data['country_name'], static fn (string $country) => blank($country) ? null : $country),
            optional($rawIpResults->data['city'], static fn (string $city) => blank($city) ? null : $city),
            optional($rawIpResults->data['latitude'], static fn (mixed $latitude) => blank($latitude) ? null : (float) $latitude),
            optional($rawIpResults->data['longitude'], static fn (mixed $longitude) => blank($longitude) ? null : (float) $longitude),
            optional($rawIpResults->data['org'], static fn (string $organization) => blank($organization) ? null : $organization),
        );
    }

    public function supports(string $driver): bool
    {
        return $driver === IpApiDotCoDriver::class;
    }
}
