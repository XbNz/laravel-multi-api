<?php

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
        return new NormalizedGeolocationResultsData(
            $rawIpResults->provider,
            $rawIpResults->data['data']['ipAddress'],
            Locale::getDisplayRegion("-{$rawIpResults->data['data']['countryCode']}", 'en'),
            null,
            null,
            null,
            $rawIpResults->data['data']['isp'],
        );
    }

    public function supports(string $driver): bool
    {
        return $driver === AbuseIpDbDotComDriver::class;
    }
}