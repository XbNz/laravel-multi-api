<?php

declare(strict_types=1);

namespace XbNz\Resolver\Domain\Ip\Mappings;

use Locale;
use XbNz\Resolver\Domain\Ip\Drivers\IpInfoDotIoDriver;
use XbNz\Resolver\Domain\Ip\DTOs\NormalizedGeolocationResultsData;
use XbNz\Resolver\Support\DTOs\RawResultsData;
use XbNz\Resolver\Support\Mappings\Mapper;

class IpInfoDotIoMapper implements Mapper
{
    public function map(RawResultsData $rawIpResults): NormalizedGeolocationResultsData
    {
        $coordinates = explode(',', $rawIpResults->data['loc']);
        $country = Locale::getDisplayRegion("-{$rawIpResults->data['country']}", 'en');

        return new NormalizedGeolocationResultsData(
            $rawIpResults->provider,
            $rawIpResults->data['ip'],
            $country,
            $rawIpResults->data['city'],
            (float) $coordinates[0],
            (float) $coordinates[1],
            $rawIpResults->data['org']
        );
    }

    public function supports(string $driver): bool
    {
        return $driver === IpInfoDotIoDriver::class;
    }
}
