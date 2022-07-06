<?php

declare(strict_types=1);

namespace XbNz\Resolver\Support\Actions;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Pool;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Promise\Utils;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Webmozart\Assert\Assert;
use XbNz\Resolver\Domain\Ip\Services\Request;
use XbNz\Resolver\Factories\GuzzleClientFactory;
use XbNz\Resolver\Factories\RawResultsFactory;
use XbNz\Resolver\Support\DTOs\RequestResponseWrapper;
use XbNz\Resolver\Support\Exceptions\ApiProviderException;

class FetchRawDataAction
{
    public function __construct(
        private readonly GuzzleClientFactory $guzzleClientFactory
    ) {
    }

    /**
     * @param array<Request> $requests
     * @return array<RequestResponseWrapper>
     */
    public function execute(array $requests): array
    {
        Assert::allImplementsInterface($requests, Request::class);

        $pools = Collection::make();
        $requestResponseData = Collection::make();

        $promises = [];
        foreach ($requests as $request) {
            $client = $this->guzzleClientFactory->for($request::class);

            HandlerStack::create()
            $pools->push(new Pool($client, [($request)()], [
                'concurrency' => Config::get('resolver.async_concurrent_requests', 10),
                'fulfilled' => static function (Response $response, $index) use ($requestResponseData, $request) {
                    $requestResponseData->push(new RequestResponseWrapper($request::class, ($request)(), $response));
                },
                'rejected' => static function (Exception $e, $index) {
                    if ($e instanceof TransferException) {
                        ApiProviderException::fromTransferException($e);
                    }

                    throw $e;
                },
            ]));
        }


        $pools->map(fn (Pool $pool) => $pool->promise())
            ->each(fn (PromiseInterface $promise) => $promise->wait());


        return $requestResponseData->toArray();
    }
}
