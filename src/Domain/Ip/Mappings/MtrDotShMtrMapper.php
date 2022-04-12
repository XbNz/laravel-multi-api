<?php

namespace XbNz\Resolver\Domain\Ip\Mappings;

use XbNz\Resolver\Domain\Ip\Drivers\MtrDotShMtrDriver;
use XbNz\Resolver\Domain\Ip\DTOs\NormalizedGeolocationResultsData;
use XbNz\Resolver\Domain\Ip\DTOs\RawIpResultsData;

class MtrDotShMtrMapper implements Mapper
{
    public function map(RawIpResultsData $rawIpResults): NormalizedGeolocationResultsData
    {
        // TODO: Use the MtrResult and MtrHop DTOs to build up a result. Find a suitable entrypoint for the client.
    }

    public function supports(string $driver): bool
    {
        return $driver === MtrDotShMtrDriver::class;
    }
}