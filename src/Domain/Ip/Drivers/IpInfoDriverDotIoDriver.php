<?php

namespace XbNz\Resolver\Domain\Ip\Drivers;

use XbNz\Resolver\Domain\Ip\Actions\GetApiKeysForDriverAction;
use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Domain\Ip\DTOs\QueriedIpData;
use XbNz\Resolver\Support\Actions\MakeHttpCallAction;
use XbNz\Resolver\Support\Drivers\Driver;

class IpInfoDriverDotIoDriver implements Driver
{
    private array $apiKeys;
    const API_URL = 'https://ipinfo.io';

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

        $coordinates = explode(',', $response['loc']);
        $country = \Locale::getDisplayRegion("-{$response['country']}", 'en');

        return new QueriedIpData(
            driver: self::class,
            ip: $ipData->ip,
            country: ,
            city: $response['city'],
            longitude: $coordinates[1],
            latitude: $coordinates[0]
        );
    }

    public function supports(): string
    {
        return 'ipInfoDotIo';
    }

    public function requiresApiKey(): bool
    {
        return true;
    }

    public function requiresFile(): bool
    {
        return false;
    }

    public function raw(IpData $ipData): array
    {
        return \Cache::remember(
            self::class . $ipData->ip,
            now()->addSeconds(config('resolver.cache_period')),
            function () use ($ipData){
                return $this->httpCallAction->execute(
                    self::API_URL . "/{$ipData->ip}",
                    $this,
                    [
                        'token' => \Arr::random($this->apiKeys),
                    ]
                )->json();
            }
        );
    }
}