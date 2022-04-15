<?php
//
//declare(strict_types=1);
//
//namespace XbNz\Resolver\Domain\Ip\Drivers;
//
//use Illuminate\Http\Client\ConnectionException;
//use Illuminate\Http\Client\RequestException;
//use Illuminate\Http\Client\Response;
//use Illuminate\Support\Arr;
//use Illuminate\Support\Facades\Cache;
//use XbNz\Resolver\Domain\Ip\DTOs\IpData;
//use XbNz\Resolver\Domain\Ip\DTOs\NormalizedGeolocationResultsData;
//use XbNz\Resolver\Support\Actions\GetRandomApiKeyAction;
//use XbNz\Resolver\Support\Actions\MakeHttpPromiseAction;
//use XbNz\Resolver\Support\Drivers\Driver;
//use XbNz\Resolver\Support\Exceptions\ApiProviderException;
//
//class IpInfoDotIoDriver implements Driver
//{
//    public const API_URL = 'https://ipinfo.io';
//
//    private array $apiKeys;
//
//    public function __construct(
//        GetRandomApiKeyAction $apiKeys,
//        private MakeHttpPromiseAction $httpPromiseAction,
//    ) {
//        $this->apiKeys = $apiKeys->execute($this);
//    }
//
//    public function query(IpData $ipData): NormalizedGeolocationResultsData
//    {
//        $response = $this->raw($ipData);
//        $coordinates = explode(',', $response['loc']);
//        $country = \Locale::getDisplayRegion("-{$response['country']}", 'en');
//
//        return new NormalizedGeolocationResultsData(
//            driver: self::class,
//            ip: $ipData->ip,
//            country: $country,
//            city: $response['city'],
//            longitude: $coordinates[1],
//            latitude: $coordinates[0]
//        );
//    }
//
//    public function supports(): string
//    {
//        return 'ipInfoDotIo';
//    }
//
//    public function requiresApiKey(): bool
//    {
//        return true;
//    }
//
//    public function requiresFile(): bool
//    {
//        return false;
//    }
//
//    public function raw(IpData $ipData): array
//    {
//        return Cache::remember(
//            self::class . $ipData->ip,
//            now()->addSeconds(config('resolver.cache_period')),
//            function () use ($ipData) {
//                return $this->resolvePromise()->json();
//            }
//        );
//    }
//
//    public function initiateAsync(IpData $ipData): void
//    {
//        $this->promise = $this->httpPromiseAction->execute(
//            self::API_URL . "/{$ipData->ip}",
//            [
//                'token' => Arr::random($this->apiKeys),
//            ]
//        );
//    }
//
//    public function resolvePromise(): Response
//    {
//        if ($this->promise === null) {
//            throw new \BadMethodCallException('Promise not initiated');
//        }
//
//        try {
//            $response = $this->promise->wait()->throw();
//        } catch (RequestException $e) {
//            $message = "{$this->supports()} has hit a snag and threw a {$e->response->status()} error";
//            throw new ApiProviderException($message);
//        } catch (ConnectionException $e) {
//            $message = "{$this->supports()} has failed to establish a connection";
//            throw new ApiProviderException($message);
//        }
//
//        return $response;
//    }
//}
