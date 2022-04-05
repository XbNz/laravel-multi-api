<?php

namespace XbNz\Resolver\Domain\Ip\Drivers;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use XbNz\Resolver\Domain\Ip\Actions\GetApiKeysForDriverAction;
use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Domain\Ip\DTOs\QueriedIpData;
use XbNz\Resolver\Support\Actions\MakeHttpPromiseAction;
use XbNz\Resolver\Support\Drivers\Driver;
use XbNz\Resolver\Support\Exceptions\ApiProviderException;

class IpApiDotComDriver implements Driver
{
    private ?PromiseInterface $promise = null;
    private array $apiKeys;
    const API_URL = 'http://api.ipapi.com/api';

    public function __construct(
        GetApiKeysForDriverAction $apiKeys,
        private MakeHttpPromiseAction $httpPromiseAction
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
        return Cache::remember(
            self::class . $ipData->ip,
            now()->addSeconds(config('resolver.cache_period')),
            function () use ($ipData) {
                return $this->resolvePromise($ipData)->json();
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
    private function handleHttpStatusCodeQuirk(Response $response): array
    {
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

    public function initiateAsync(IpData $ipData): void
    {
        $this->promise = $this->httpPromiseAction->execute(
            self::API_URL . "/{$ipData->ip}",
            [
                'access_key' => Arr::random($this->apiKeys),
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

        $this->handleHttpStatusCodeQuirk($response);

        return $response;
    }
}