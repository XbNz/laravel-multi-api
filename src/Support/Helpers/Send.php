<?php

namespace XbNz\Resolver\Support\Helpers;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Pool;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Webmozart\Assert\Assert;
use XbNz\Resolver\Support\DTOs\RequestResponseWrapper;
use XbNz\Resolver\Support\Exceptions\ApiProviderException;

class Send
{
    /**
     * @param array<Request> $requests
     * @return array<RequestResponseWrapper>
     */
    public static function async(Client $client, array $requests): array
    {
        Assert::allIsInstanceOf($requests, Request::class);

        $requestResponseData = Collection::make();

        (new Pool($client, $requests, [
            'concurrency' => Config::get('resolver.async_concurrent_requests', 10),
            'fulfilled' => static function (Response $response, int $index) use ($requestResponseData, $requests) {
                $requestResponseData->push(new RequestResponseWrapper($requests[$index], $response));
            },
            'rejected' => static function (Exception $e, $index) {
                if ($e instanceof TransferException) {
                    ApiProviderException::fromTransferException($e);
                }

                throw $e;
            },
        ]))->promise()->wait();

        return $requestResponseData->toArray();
    }

    public static function sync(Client $client, Request $request): RequestResponseWrapper
    {
        try {
            $response = $client->send($request);
        } catch (Exception $e) {
            if ($e instanceof TransferException) {
                ApiProviderException::fromTransferException($e);
            }

            throw $e;
        }

        return new RequestResponseWrapper($request, $response);
    }
}