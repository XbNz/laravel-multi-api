<?php

namespace XbNz\Resolver\Domain\Ip\Drivers;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Webmozart\Assert\Assert;
use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Support\Drivers\Driver;

class AbuseIpDbDotComDriver implements Driver
{

    const API_URL = 'https://api.abuseipdb.com/api/v2/check/';

    public function getRequests(array $dataObjects): Collection
    {
        Assert::allIsInstanceOf($dataObjects, IpData::class, '$dataObjects must be an array of IpData objects');

        $generator = static function (array $ipDataObjects) {
            foreach ($ipDataObjects as $ipData) {
                $uri = Uri::withQueryValues(new Uri(self::API_URL), [
                    'ipAddress' => $ipData->ip,
                    'maxAgeInDays' => '365',
                ]);
                yield new Request('GET', $uri);
            }
        };

        return new Collection(iterator_to_array($generator($dataObjects)));
    }

    public function supports(string $driver): bool
    {
        return $driver === __CLASS__;
    }
}