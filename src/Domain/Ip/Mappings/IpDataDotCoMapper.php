<?php

declare(strict_types=1);

namespace XbNz\Resolver\Domain\Ip\Mappings;

use XbNz\Resolver\Domain\Ip\Drivers\IpDataDotCoDriver;
use XbNz\Resolver\Domain\Ip\DTOs\NormalizedGeolocationResultsData;
use XbNz\Resolver\Support\DTOs\RequestResponseWrapper;
use XbNz\Resolver\Support\Mappings\Mapper;

class IpDataDotCoMapper implements Mapper
{
    public function map(RequestResponseWrapper $rawIpResults): NormalizedGeolocationResultsData
    {
        return new NormalizedGeolocationResultsData(
            $rawIpResults->request,
            $rawIpResults->data['ip'],
            optional($rawIpResults->data['country_name'] ?? null, static fn (string $country) => blank($country) ? null : $country),
            optional($rawIpResults->data['city'] ?? null, static fn (string $city) => blank($city) ? null : $city),
            optional($rawIpResults->data['latitude'] ?? null, static fn (mixed $latitude) => blank($latitude) ? null : (float) $latitude),
            optional($rawIpResults->data['longitude'] ?? null, static fn (mixed $longitude) => blank($longitude) ? null : (float) $longitude),
            optional($rawIpResults->data['asn']['name'] ?? null, static fn (string $organization) => blank($organization) ? null : $organization),
        );
    }

    public function supports(string $request): bool
    {
        return $request === IpDataDotCoDriver::class;
    }
}
