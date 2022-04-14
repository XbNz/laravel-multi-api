<?php

namespace XbNz\Resolver\Tests\Feature\Fakes;

use GuzzleHttp\Middleware;
use XbNz\Resolver\Support\Strategies\RetryStrategy;

class FakeGuzzleRetryStrategy implements RetryStrategy
{

    public function guzzleMiddleware(...$gibberish): callable
    {
        return Middleware::mapResponse(static function (\Psr\Http\Message\ResponseInterface $response) {
            // Do nothing with the response
        });
    }

    public function supports(string $driver): bool
    {
        return $driver === FakeDriver::class;
    }
}