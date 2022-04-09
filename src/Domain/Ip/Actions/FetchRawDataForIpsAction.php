<?php

namespace XbNz\Resolver\Domain\Ip\Actions;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Pool;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Utils;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Domain\Ip\Factories\GuzzleIpClientFactory;
use XbNz\Resolver\Support\Drivers\Driver;

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
     */
    public function execute(array $ipDataObjects, array $providers): array
    {
        $pools = Collection::make();

        foreach ($providers as $provider) {
            [$requests, $builders] = Collection::make($this->drivers)
                ->sole(fn (Driver $driver) => $driver->supports($provider))
                ->getRequests($ipDataObjects)
                ->partition(fn ($requestOrBuilder) => $requestOrBuilder instanceof RequestInterface);

            $client = $this->guzzleIpClientFactory->for($provider);


            $pools->push(new Pool($client, $requests->toArray(), [
                'concurrency' => Config::get('resolver.async_concurrent_requests', 10),
                'fulfilled' => static function (Response $response, $index) {

                },
                'rejected' => static function (RequestException $reason, $index) {

                },
            ]));
        }


        $pools->map(fn (Pool $pool) => $pool->promise())
            ->each(fn (PromiseInterface $promise) => $promise->wait());


    }
}