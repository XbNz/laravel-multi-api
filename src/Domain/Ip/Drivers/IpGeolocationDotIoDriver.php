<?php

declare(strict_types=1);

namespace XbNz\Resolver\Domain\Ip\Drivers;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Webmozart\Assert\Assert;
use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Domain\Ip\DTOs\QueriedIpData;
use XbNz\Resolver\Domain\Ip\Factories\GuzzleIpClientFactory;
use XbNz\Resolver\Support\Actions\GetRandomApiKeyAction;
use XbNz\Resolver\Support\Actions\MakeHttpPromiseAction;
use XbNz\Resolver\Support\Drivers\Driver;
use XbNz\Resolver\Support\Exceptions\ApiProviderException;

class IpGeolocationDotIoDriver implements Driver
{
    const API_URL = 'https://api.ipgeolocation.io/ipgeo/';

    public function getRequests(array $ipDataObjects): Collection
    {
        Assert::allIsInstanceOf($ipDataObjects, IpData::class, '$ipDataObjects must be an array of IpData objects');

        $generator = static function (array $ipDataObjects) {
            foreach ($ipDataObjects as $ipData) {
                $uri = Uri::withQueryValue(new Uri(self::API_URL), 'ip', $ipData->ip);
                yield new Request('GET', $uri);
            }
        };

        return new Collection(iterator_to_array($generator($ipDataObjects)));
    }

    public function supports(string $provider): bool
    {
        return Str::of($provider)
            ->lower()
            ->contains('ipgeolocation.io');
    }
}