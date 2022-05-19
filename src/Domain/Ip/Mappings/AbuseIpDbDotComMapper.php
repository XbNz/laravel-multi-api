<?php

declare(strict_types=1);

namespace XbNz\Resolver\Domain\Ip\Mappings;

use Locale;
use XbNz\Resolver\Domain\Ip\Drivers\AbuseIpDbDotComDriver;
use XbNz\Resolver\Domain\Ip\DTOs\NormalizedGeolocationResultsData;
use XbNz\Resolver\Support\DTOs\RawResultsData;
use XbNz\Resolver\Support\Mappings\Mapper;

class AbuseIpDbDotComMapper implements Mapper
{
    public function map(RawResultsData $rawIpResults): NormalizedGeolocationResultsData
    {
        if ($rawIpResults->data['data']['countryCode'] !== null) {
            $country = Locale::getDisplayRegion("-{$rawIpResults->data['data']['countryCode']}", 'en');
        }

        return new NormalizedGeolocationResultsData(
            $rawIpResults->provider,
            $rawIpResults->data['data']['ipAddress'],
            $country ?? null,
            organization: optional($rawIpResults->data['data']['isp'] ?? null, static fn (string $organization) => blank($organization) ? null : $organization),
        );
    }

    public function supports(string $driver): bool
    {
        return $driver === AbuseIpDbDotComDriver::class;
    }
}
