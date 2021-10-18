<?php

namespace XbNz\Resolver\Domain\Ip\Drivers;

use XbNz\Resolver\Domain\Ip\Actions\GetApiKeysForDriverAction;
use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Domain\Ip\DTOs\QueriedIpData;
use XbNz\Resolver\Support\Actions\MakeHttpCallAction;

class IpInfoDriver implements Driver
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

    }

    public function supports(): string
    {
        return 'ipInfo';
    }

    public function requiresApiKey(): bool
    {
        return true;
    }

    public function requiresFile(): bool
    {
        return false;
    }

    private function raw(IpData $ipData): array
    {
        return \Cache::remember(
            self::class . $ipData->ip,
            now()->addSeconds(config('resolver.cache_period')),
            function () use ($ipData){
                return $this->httpCallAction->execute(
                    self::API_URL . "/{$ipData->ip}",
                    ['token' => \Arr::random($this->apiKeys)]
                )->json();
            }
            // TODO: Make some more drivers and test them. Find a good way to switch between raw and normalized.
        );
    }
}