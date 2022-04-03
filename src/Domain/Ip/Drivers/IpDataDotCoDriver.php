<?php

namespace XbNz\Resolver\Domain\Ip\Drivers;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use XbNz\Resolver\Domain\Ip\Actions\GetApiKeysForDriverAction;
use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Domain\Ip\DTOs\QueriedIpData;
use XbNz\Resolver\Support\Actions\MakeHttpCallAction;
use XbNz\Resolver\Support\Drivers\Driver;

class IpDataDotCoDriver implements Driver
{
    private array $apiKeys;
    const API_URL = 'https://api.ipdata.co';

    public function __construct(
        GetApiKeysForDriverAction $apiKeys,
        private MakeHttpCallAction $httpCallAction,
    ) {
        $this->apiKeys = $apiKeys->execute($this);
    }

    public function query(IpData $ipData): QueriedIpData
    {
        $response = $this->raw($ipData);

        return new QueriedIpData(
            driver: self::class,
            ip: $ipData->ip,
            country: $response['country_name'] ?? 'N/A',
            city: $response['city'] ?? 'N/A',
            longitude: $response['longitude'],
            latitude: $response['latitude'],
        );
    }

    public function supports(): string
    {
        return 'ipDataDotCo';
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
        return Cache::remember(
            self::class . $ipData->ip,
            now()->addSeconds(config('resolver.cache_period')),
            function () use ($ipData){
                return $this->httpCallAction->execute(
                    self::API_URL . '/' . $ipData->ip,
                    $this,
                    [
                        'api-key' => Arr::random($this->apiKeys),
                    ]
                )->json();
            }
        );
    }


}