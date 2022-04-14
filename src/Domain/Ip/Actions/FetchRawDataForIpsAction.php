<?php

namespace XbNz\Resolver\Domain\Ip\Actions;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Pool;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Psr\Http\Message\RequestInterface;
use Webmozart\Assert\Assert;
use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Domain\Ip\DTOs\RawIpResultsData;
use XbNz\Resolver\Factories\Ip\GuzzleIpClientFactory;
use XbNz\Resolver\Factories\Ip\RawIpResultsDataFactory;
use XbNz\Resolver\Support\Drivers\Driver;
use XbNz\Resolver\Support\Exceptions\ApiProviderException;

class FetchRawDataForIpsAction
{
    /**
     * @param array<Driver> $drivers
     */
    public function __construct(
        private GuzzleIpClientFactory $guzzleIpClientFactory
    )
    {}

    /**
     * @param array<IpData> $ipDataObjects
     * @param array $drivers
     * @return array<RawIpResultsData>
     */
    public function execute(array $ipDataObjects, array $drivers): array
    {
        Assert::allIsInstanceOf($ipDataObjects, IpData::class);
        Assert::allImplementsInterface($drivers, Driver::class);

        $pools = Collection::make();
        $rawIpResultsData = Collection::make();

        foreach ($drivers as $driver) {
            [$requests, $builders] = Collection::make($driver->getRequests($ipDataObjects))
                ->partition(fn ($requestOrBuilder) => $requestOrBuilder instanceof RequestInterface);

            // TODO: Consider bringing injection back for a more streamlined all ::class approach. This is now the
            // only class that needs an instantiated object rather than a class name.

            $client = $this->guzzleIpClientFactory->for($driver::class);

            $pools->push(new Pool($client, $requests->toArray(), [
                'concurrency' => Config::get('resolver.async_concurrent_requests', 10),
                'fulfilled' => static function (Response $response, $index) use ($rawIpResultsData, $driver) {
                    $rawIpResultsData->push(RawIpResultsDataFactory::fromResponse($response, $driver));
                },
                'rejected' => static function (RequestException $e, $index) {
                    ApiProviderException::fromRequestException($e);
                },
            ]));
        }

        $pools->map(fn (Pool $pool) => $pool->promise())
            ->each(fn (PromiseInterface $promise) => $promise->wait());

        return $rawIpResultsData->toArray();
    }
}