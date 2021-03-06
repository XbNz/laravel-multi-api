<?php

declare(strict_types=1);

namespace XbNz\Resolver\Domain\Ip\Mappings;

use XbNz\Resolver\Domain\Ip\Drivers\AbstractApiDotComDriver;
use XbNz\Resolver\Domain\Ip\DTOs\NormalizedGeolocationResultsData;
use XbNz\Resolver\Support\DTOs\RawResultsData;
use XbNz\Resolver\Support\Mappings\Mapper;

class AbstractApiDotComMapper implements Mapper
{
    public function map(RawResultsData $rawIpResults): NormalizedGeolocationResultsData
    {
        return new NormalizedGeolocationResultsData(
            $rawIpResults->provider,
            $rawIpResults->data['ip_address'],
            optional($rawIpResults->data['country'] ?? null, static fn (string $country) => blank($country) ? null : $country),
            optional($rawIpResults->data['city'] ?? null, static fn (string $city) => blank($city) ? null : $city),
            optional($rawIpResults->data['latitude'] ?? null, static fn (mixed $latitude) => blank($latitude) ? null : (float) $latitude),
            optional($rawIpResults->data['longitude'] ?? null, static fn (mixed $longitude) => blank($longitude) ? null : (float) $longitude),
            optional($rawIpResults->data['connection']['isp_name'] ?? null, static fn (string $organization) => blank($organization) ? null : $organization),
        );
    }

//    /**
//     * @template T
//     *
//     * @param T $driver
//     * @return T|null
//     */
//    private function getValue(mixed $value)
//    {
//        if (blank($value)) {
//            return null;
//        }
//
//        return $value;
//    }

    public function supports(string $driver): bool
    {
        return $driver === AbstractApiDotComDriver::class;
    }
}
