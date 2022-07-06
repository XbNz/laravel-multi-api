<?php

declare(strict_types=1);

namespace XbNz\Resolver\Domain\Ip\Mappings;

use Locale;
use XbNz\Resolver\Domain\Ip\Drivers\IpInfoDotIoDriver;
use XbNz\Resolver\Domain\Ip\DTOs\NormalizedGeolocationResultsData;
use XbNz\Resolver\Support\DTOs\RequestResponseWrapper;
use XbNz\Resolver\Support\Mappings\Mapper;

class IpInfoDotIoMapper implements Mapper
{
    public function map(RequestResponseWrapper $rawIpResults): NormalizedGeolocationResultsData
    {
        if ($rawIpResults->data['loc'] !== null) {
            $coordinates = explode(',', $rawIpResults->data['loc']);
        }

        if ($rawIpResults->data['country'] !== null) {
            $country = Locale::getDisplayRegion("-{$rawIpResults->data['country']}", 'en');
        }

        return new NormalizedGeolocationResultsData(
            $rawIpResults->request,
            $rawIpResults->data['ip'],
            $country ?? null,
            optional($rawIpResults->data['city'] ?? null, static fn (string $city) => blank($city) ? null : $city),
            optional($coordinates[0] ?? null, static fn ($latitude) => blank($latitude) ? null : (float) $latitude),
            optional($coordinates[1] ?? null, static fn ($longitude) => blank($longitude) ? null : (float) $longitude),
            optional($rawIpResults->data['org'] ?? null, static fn (string $organization) => blank($organization) ? null : $organization),
        );
    }

    public function supports(string $request): bool
    {
        return $request === IpInfoDotIoDriver::class;
    }
}
