<?php

declare(strict_types=1);

namespace XbNz\Resolver\Domain\Ip\Mappings;

use Locale;
use XbNz\Resolver\Domain\Ip\Drivers\AbuseIpDbDotComDriver;
use XbNz\Resolver\Domain\Ip\DTOs\NormalizedGeolocationResultsData;
use XbNz\Resolver\Support\DTOs\RequestResponseWrapper;
use XbNz\Resolver\Support\Mappings\Mapper;

class AbuseIpDbDotComMapper implements Mapper
{
    public function map(RequestResponseWrapper $rawIpResults): NormalizedGeolocationResultsData
    {
        if ($rawIpResults->data['data']['countryCode'] !== null) {
            $country = Locale::getDisplayRegion("-{$rawIpResults->data['data']['countryCode']}", 'en');
        }

        return new NormalizedGeolocationResultsData(
            $rawIpResults->request,
            $rawIpResults->data['data']['ipAddress'],
            $country ?? null,
            organization: optional($rawIpResults->data['data']['isp'] ?? null, static fn (string $organization) => blank($organization) ? null : $organization),
        );
    }

    public function supports(string $request): bool
    {
        return $request === AbuseIpDbDotComDriver::class;
    }
}
