<?php

declare(strict_types=1);

namespace XbNz\Resolver\Support\Actions;

use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Pool;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Psr\Http\Message\RequestInterface;
use Webmozart\Assert\Assert;
use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Factories\GuzzleClientFactory;
use XbNz\Resolver\Factories\RawResultsFactory;
use XbNz\Resolver\Support\Drivers\Driver;
use XbNz\Resolver\Support\DTOs\RawResultsData;
use XbNz\Resolver\Support\Exceptions\ApiProviderException;

class FetchRawDataAction
{
    public function __construct(
        private GuzzleClientFactory $guzzleClientFactory
    ) {
    }

    /**
     * @param array<IpData> $dataObjects
     * @param array<Driver> $drivers
     * @return array<RawResultsData>
     */
    public function execute(array $dataObjects, array $drivers): array
    {
        Assert::allImplementsInterface($drivers, Driver::class);

        $pools = Collection::make();
        $rawResultsData = Collection::make();

        foreach ($drivers as $driver) {
            [$requests,] = Collection::make($driver->getRequests($dataObjects))
                ->partition(fn ($requestOrBuilder) => $requestOrBuilder instanceof RequestInterface);

            $client = $this->guzzleClientFactory->for($driver::class);

            $pools->push(new Pool($client, $requests->toArray(), [
                'concurrency' => Config::get('resolver.async_concurrent_requests', 10),
                'fulfilled' => static function (Response $response, $index) use ($rawResultsData, $driver) {
                    $rawResultsData->push(RawResultsFactory::fromResponse($response, $driver::class));
                },
                'rejected' => static function (\Exception $e, $index) {
                    if ($e instanceof TransferException) {
                        ApiProviderException::fromTransferException($e);
                    }

                    throw $e;
                },
            ]));
        }


        $pools->map(fn (Pool $pool) => $pool->promise())
            ->each(fn (PromiseInterface $promise) => $promise->wait());

        return $rawResultsData->toArray();
    }
}
