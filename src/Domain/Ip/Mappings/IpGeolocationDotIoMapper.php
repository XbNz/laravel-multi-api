<?php

namespace XbNz\Resolver\Domain\Ip\Mappings;

use XbNz\Resolver\Domain\Ip\Drivers\IpGeolocationDotIoDriver;
use XbNz\Resolver\Domain\Ip\DTOs\NormalizedGeolocationResultsData;
use XbNz\Resolver\Support\DTOs\RawResultsData;
use XbNz\Resolver\Support\Mappings\Mapper;

class IpGeolocationDotIoMapper implements Mapper
{

    public function map(RawResultsData $rawIpResults): NormalizedGeolocationResultsData
    {
        return new NormalizedGeolocationResultsData(
            $rawIpResults->provider,
            $rawIpResults->data['ip'],
            $rawIpResults->data['country_name'],
            $rawIpResults->data['city'],
            $rawIpResults->data['latitude'],
            $rawIpResults->data['longitude'],
            $rawIpResults->data['organization']
        );
    }

    public function supports(string $driver): bool
    {
        return $driver === IpGeolocationDotIoDriver::class;
    }
}