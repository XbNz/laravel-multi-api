<?php

declare(strict_types=1);

namespace XbNz\Resolver\Domain\Ip\Drivers;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Webmozart\Assert\Assert;
use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Domain\Ip\DTOs\NormalizedGeolocationResultsData;
use XbNz\Resolver\Support\Actions\GetRandomApiKeyAction;
use XbNz\Resolver\Support\Actions\MakeHttpPromiseAction;
use XbNz\Resolver\Support\Drivers\Driver;
use XbNz\Resolver\Support\Exceptions\ApiProviderException;

class IpInfoDotIoDriver implements Driver
{
    public const API_URL = 'https://ipinfo.io/json/';

    public function getRequests(array $dataObjects): Collection
    {
        Assert::allIsInstanceOf($dataObjects, IpData::class, '$dataObjects must be an array of IpData objects');

        $generator = static function (array $ipDataObjects) {
            foreach ($ipDataObjects as $ipData) {
                $uri = (new Uri(self::API_URL))->withPath('/' . $ipData->ip);
                yield (new Request('GET', $uri))->withHeader('Accept', 'application/json');
            }
        };

        return new Collection(iterator_to_array($generator($dataObjects)));
    }

    public function supports(string $driver): bool
    {
        return $driver === __CLASS__;
    }
}
