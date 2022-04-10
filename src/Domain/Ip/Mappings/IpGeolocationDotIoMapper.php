<?php

namespace XbNz\Resolver\Domain\Ip\Mappings;

use Illuminate\Support\Str;
use XbNz\Resolver\Domain\Ip\DTOs\NormalizedIpResultsData;
use XbNz\Resolver\Domain\Ip\DTOs\RawIpResultsData;

class IpGeolocationDotIoMapper implements Mapper
{

    public function map(RawIpResultsData $rawIpResults): NormalizedIpResultsData
    {
        return new NormalizedIpResultsData(
            $rawIpResults->provider,
            $rawIpResults->data['ip'],
            $rawIpResults->data['country_name'],
            $rawIpResults->data['city'],
            $rawIpResults->data['latitude'],
            $rawIpResults->data['longitude'],
            $rawIpResults->data['organization']
        );
    }

    public function supports(string $provider): bool
    {
        return Str::of($provider)
            ->lower()
            ->contains('ipgeolocation.io');
    }
}