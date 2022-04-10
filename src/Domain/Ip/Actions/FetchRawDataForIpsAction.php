<?php

namespace XbNz\Resolver\Domain\Ip\Actions;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Pool;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Psr\Http\Message\RequestInterface;
use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Domain\Ip\DTOs\RawIpResultsData;
use XbNz\Resolver\Factories\GuzzleIpClientFactory;
use XbNz\Resolver\Factories\RawIpResultsDataFactory;
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
     * @param array $providers
     * @return array<RawIpResultsData>
     */
    public function execute(array $ipDataObjects, array $providers): array
    {
        $pools = Collection::make();
        $rawIpResultsData = Collection::make();

        foreach ($providers as $provider) {
            [$requests, $builders] = Collection::make($this->drivers)
                ->sole(fn (Driver $driver) => $driver->supports($provider))
                ->getRequests($ipDataObjects)
                ->partition(fn ($requestOrBuilder) => $requestOrBuilder instanceof RequestInterface);

            $client = $this->guzzleIpClientFactory->for($provider);

            $pools->push(new Pool($client, $requests->toArray(), [
                'concurrency' => Config::get('resolver.async_concurrent_requests', 10),
                'fulfilled' => static function (Response $response, $index) use ($rawIpResultsData, $provider) {
                    $rawIpResultsData->push(RawIpResultsDataFactory::fromResponse($response, $provider));
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