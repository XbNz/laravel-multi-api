<?php

namespace XbNz\Resolver\Domain\Ip\Drivers;


use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Webmozart\Assert\Assert;
use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Support\Drivers\Driver;

class IpDataDotCoDriver implements Driver
{
    const API_URL = 'https://api.ipdata.co';


    public function getRequests(array $ipDataObjects): Collection
    {
        Assert::allIsInstanceOf($ipDataObjects, IpData::class, '$ipDataObjects must be an array of IpData objects');

        $generator = static function (array $ipDataObjects) {
            foreach ($ipDataObjects as $ipData) {
                $uri = (new Uri(self::API_URL))->withPath('/' . $ipData->ip);
                yield new Request('GET', $uri);
            }
        };

        return new Collection(iterator_to_array($generator($ipDataObjects)));
    }

    public function supports(string $provider): bool
    {
        return Str::of($provider)
            ->lower()
            ->contains('ipdata.co');
    }
}