<?php

namespace XbNz\Resolver\Domain\Ip\Drivers;

use Illuminate\Support\Facades\Cache;
use XbNz\Resolver\Domain\Ip\Actions\GetApiKeysForDriverAction;
use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Domain\Ip\DTOs\QueriedIpData;
use XbNz\Resolver\Support\Actions\MakeHttpCallAction;
use XbNz\Resolver\Support\Drivers\Driver;
use XbNz\Resolver\Support\Exceptions\ApiProviderException;

class IpApiDotComDriver implements Driver
{
    private array $apiKeys;
    const API_URL = 'http://api.ipapi.com/api';

    public function __construct(
        GetApiKeysForDriverAction $apiKeys,
        private MakeHttpCallAction $httpCallAction
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

    public function raw(IpData $ipData): array
    {
        return \Cache::remember(
            self::class . $ipData->ip,
            now()->addSeconds(config('resolver.cache_period')),
            function () use ($ipData) {
                return $this->handleHttpStatusCodeQuirk($ipData);
            }
        );
    }

    public function supports(): string
    {
        return 'ipApiDotCom';
    }

    public function requiresApiKey(): bool
    {
        return true;
    }


    /**
     * This API provider has obliterated all REST conventions and is throwing a 200 code
     * for failed authentication.
     */
    private function handleHttpStatusCodeQuirk(IpData $ipData): array
    {
        $response = $this->httpCallAction->execute(
            self::API_URL . "/{$ipData->ip}",
            $this,
            [
                'access_key' => \Arr::random($this->apiKeys),
            ]
        );

        if (! isset($response->json()['success']) || $response->json()['success'] === true) {
            return $response->json();
        }

        throw new ApiProviderException(
            "{$this->supports()} has hit a snag and threw a {$response->status()} status code. Yes, 200: you saw that right. TWO HUNDRED. Go complain to the API devs."
        );
    }

    public function requiresFile(): bool
    {
        return false;
    }
}