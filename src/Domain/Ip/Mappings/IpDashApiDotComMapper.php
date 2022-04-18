<?php

namespace XbNz\Resolver\Domain\Ip\Mappings;

use XbNz\Resolver\Domain\Ip\Drivers\IpDashApiDotComDriver;
use XbNz\Resolver\Domain\Ip\DTOs\NormalizedGeolocationResultsData;
use XbNz\Resolver\Support\DTOs\RawResultsData;
use XbNz\Resolver\Support\Mappings\Mapper;

class IpDashApiDotComMapper implements Mapper
{
    public function map(RawResultsData $rawIpResults): NormalizedGeolocationResultsData
    {
        return new NormalizedGeolocationResultsData(
            $rawIpResults->provider,
            $rawIpResults->data['query'],
            $rawIpResults->data['country'],
            $rawIpResults->data['city'],
            $rawIpResults->data['lat'],
            $rawIpResults->data['lon'],
            $rawIpResults->data['as'],
        );
    }

    public function supports(string $driver): bool
    {
        return $driver === IpDashApiDotComDriver::class;
    }
}