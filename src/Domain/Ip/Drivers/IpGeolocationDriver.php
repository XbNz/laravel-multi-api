<?php

namespace XbNz\Resolver\Domain\Ip\Drivers;

use XbNz\Resolver\Domain\Ip\Actions\GetApiKeysForDriverAction;
use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Domain\Ip\DTOs\QueriedIpData;

class IpGeolocationDriver implements Driver
{
    private array $apiKeys;
    const API_URL = 'https://api.ipgeolocation.io/ipgeo';

    public function __construct(
        GetApiKeysForDriverAction $apiKeys
    )
    {
        $this->apiKeys = $apiKeys->execute($this);
    }

    public function query(IpData $ipData): QueriedIpData
    {
        $response = \Http::get(self::API_URL, [
            'apiKey' => $this->apiKeys[array_rand($this->apiKeys)],
            'ip' => $ipData->ip
        ])->json();


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
        return 'ipGeolocation';
    }

    public function requiresApiKey(): bool
    {
        // TODO: Implement requiresApiKey() method.
    }

    public function requiresFile(): bool
    {
        // TODO: Implement requiresFile() method.
    }
}