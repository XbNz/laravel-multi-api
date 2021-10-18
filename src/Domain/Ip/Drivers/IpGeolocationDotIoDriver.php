<?php

namespace XbNz\Resolver\Domain\Ip\Drivers;

use XbNz\Resolver\Domain\Ip\Actions\GetApiKeysForDriverAction;
use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Domain\Ip\DTOs\QueriedIpData;
use XbNz\Resolver\Support\Actions\MakeHttpCallAction;
use XbNz\Resolver\Support\Drivers\Driver;

class IpGeolocationDotIoDriver implements Driver
{
    private array $apiKeys;
    const API_URL = 'https://api.ipgeolocation.io/ipgeo';

    public function __construct(
        GetApiKeysForDriverAction $apiKeys,
        private MakeHttpCallAction $httpCallAction,
    )
    {
        $this->apiKeys = $apiKeys->execute($this);
    }

    public function query(IpData $ipData): QueriedIpData
    {
        $response = $this->raw($ipData);

        return new QueriedIpData(
            driver: self::class,
            ip: $ipData->ip,
            country: $response['country_name'],
            city: $response['city'],
            longitude: $response['longitude'],
            latitude: $response['latitude']
        );
    }

    public function supports(): string
    {
        return 'ipGeolocationDotIo';
    }

    public function requiresApiKey(): bool
    {
        // TODO: Implement requiresApiKey() method.
    }

    public function requiresFile(): bool
    {
        // TODO: Implement requiresFile() method.
    }

    public function raw(IpData $ipData): array
    {
        return \Cache::remember(
            self::class . $ipData->ip,
            now()->addSeconds(config('resolver.cache_period')),
            function () use ($ipData){
                return $this->httpCallAction->execute(
                    self::API_URL,
                    $this,
                    [
                        'apiKey' => \Arr::random($this->apiKeys),
                        'ip' => $ipData->ip
                    ]
                )->json();
            }
        );
    }


}