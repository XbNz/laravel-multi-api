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

    public function execute(array $ipDataObjects, string $provider): array
    {
        [$requests, $builders] = Collection::make($this->drivers)
            ->sole(fn (Driver $driver) => $driver->supports($provider))
            ->getRequests($ipDataObjects)
            ->partition(fn ($requestOrBuilder) => $requestOrBuilder instanceof RequestInterface);

        $client = $this->guzzleIpClientFactory->for($provider);


        $pool = new Pool($client, $requests->toArray(), [
            'concurrency' => Config::get('resolver.async_concurrent_requests', 10),
            'fulfilled' => static function (Response $response, $index) {
                dump(Utils::jsonDecode($response->getBody()->getContents(), true));
            },
            'rejected' => static function (RequestException $reason, $index) {
                // this is delivered each failed request
            },
        ]);

        $pool->promise()->wait();




    }
}