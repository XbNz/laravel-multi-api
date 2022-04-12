<?php

namespace XbNz\Resolver\Domain\Ip\Actions;

use GuzzleHttp\Pool;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Psr\Http\Message\RequestInterface;
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
        private array $drivers,
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
        $pools = Collection::make();
        $rawIpResultsData = Collection::make();

        foreach ($drivers as $driver) {
            [$requests, $builders] = Collection::make($this->drivers)
                ->sole(fn (Driver $driverObj) => $driverObj->supports($driver))
                ->getRequests($ipDataObjects)
                ->partition(fn ($requestOrBuilder) => $requestOrBuilder instanceof RequestInterface);

            $client = $this->guzzleIpClientFactory->for($driver);

            $pools->push(new Pool($client, $requests->toArray(), [
                'concurrency' => Config::get('resolver.async_concurrent_requests', 10),
                'fulfilled' => static function (Response $response, $index) use ($rawIpResultsData, $driver) {
                    $rawIpResultsData->push(RawIpResultsDataFactory::fromResponse($response, $driver));
                },
                'rejected' => static function (\Throwable $reason, $index) {
                    throw new ApiProviderException($reason->getMessage(), $reason->getCode(), $reason);
                },
            ]));
        }

        $pools->map(fn (Pool $pool) => $pool->promise())
            ->each(fn (PromiseInterface $promise) => $promise->wait());

        return $rawIpResultsData->toArray();
    }
}