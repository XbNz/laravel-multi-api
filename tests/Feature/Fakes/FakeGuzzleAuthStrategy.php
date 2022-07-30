<?php

declare(strict_types=1);

namespace XbNz\Resolver\Tests\Feature\Fakes;

use GuzzleHttp\Middleware;
use XbNz\Resolver\Support\Strategies\AuthStrategy;

class FakeGuzzleAuthStrategy implements AuthStrategy
{
    public function guzzleMiddleware(...$gibberish): callable
    {
        return Middleware::mapResponse(static function (\Psr\Http\Message\ResponseInterface $response) {
            // Do nothing with the response
        });
    }

    public function supports(string $service): bool
    {
        return $service === FakeDriver::class;
    }
}
