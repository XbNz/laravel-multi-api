<?php

namespace XbNz\Resolver\Domain\Ip\Mappings;

use Illuminate\Support\Str;
use Locale;
use XbNz\Resolver\Domain\Ip\Drivers\AbuseIpDbDotComDriver;
use XbNz\Resolver\Domain\Ip\DTOs\NormalizedIpResultsData;
use XbNz\Resolver\Domain\Ip\DTOs\RawIpResultsData;

class AbuseIpDbDotComMapper implements Mapper
{
    public function map(RawIpResultsData $rawIpResults): NormalizedIpResultsData
    {
        return new NormalizedIpResultsData(
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