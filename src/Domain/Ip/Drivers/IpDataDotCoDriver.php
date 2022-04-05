<?php

namespace XbNz\Resolver\Domain\Ip\Drivers;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Webmozart\Assert\Assert;
use XbNz\Resolver\Domain\Ip\Actions\GetApiKeysForDriverAction;
use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Domain\Ip\DTOs\QueriedIpData;
use XbNz\Resolver\Support\Actions\MakeHttpPromiseAction;
use XbNz\Resolver\Support\Drivers\Driver;
use XbNz\Resolver\Support\Exceptions\ApiProviderException;

class IpDataDotCoDriver implements Driver
{
    private array $apiKeys;
    private ?PromiseInterface $promise = null;
    const API_URL = 'https://api.ipdata.co';

    public function __construct(
        GetApiKeysForDriverAction     $apiKeys,
        private MakeHttpPromiseAction $httpPromiseAction,
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

    public function initiateAsync(IpData $ipData): void
    {
        $this->promise = $this->httpPromiseAction->execute(
                self::API_URL . '/' . $ipData->ip,
                [
                    'api-key' => Arr::random($this->apiKeys),
                ]
            );
    }

    public function resolvePromise(): Response
    {
        if ($this->promise === null) {
            throw new \BadMethodCallException('Promise not initiated');
        }

        try {
            $response = $this->promise->wait()->throw();
        } catch (RequestException $e) {
            $message = "{$this->supports()} has hit a snag and threw a {$e->response->status()} error";
            throw new ApiProviderException($message);
        } catch (ConnectionException $e) {
            $message = "{$this->supports()} has failed to establish a connection";
            throw new ApiProviderException($message);
        }

        return $response;
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
                return $this->resolvePromise()->json();
            }
        );
    }


}