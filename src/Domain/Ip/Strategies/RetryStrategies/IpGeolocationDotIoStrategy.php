<?php

declare(strict_types=1);

namespace XbNz\Resolver\Domain\Ip\Strategies\RetryStrategies;

use GuzzleHttp\Psr7\Uri;
use Illuminate\Support\Facades\Config;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use XbNz\Resolver\Domain\Ip\Drivers\IpGeolocationDotIoDriver;
use XbNz\Resolver\Support\Actions\GetRandomApiKeyAction;
use XbNz\Resolver\Support\Guzzle\Middlewares\WithRetry;
use XbNz\Resolver\Support\Strategies\RetryStrategy;

class IpGeolocationDotIoStrategy implements RetryStrategy
{
    public function __construct(
        private GetRandomApiKeyAction $getRandomApiKey,
    )
    {}

    public function guzzleMiddleware(): callable
    {
        return (new WithRetry)(
            Config::get('resolver.tries', 5),
            Config::get('resolver.retry_sleep', 2),
            Config::get('retry_sleep_multiplier', 1.5),
            runOnRetryCallable: function (
                int $attemptNumber,
                float $delay,
                RequestInterface &$request,
                array &$options,
                ?ResponseInterface $response
            ) {
                $uri = $request->getUri();
                $randomKey = $this->getRandomApiKey->execute(IpGeolocationDotIoDriver::class, 'ip-resolver.api-keys');

                $newUri = Uri::withQueryValue($uri, 'apiKey', $randomKey);
                $request->withUri($newUri);
            }
        );
    }

    public function supports(string $driver): bool
    {
        return $driver === IpGeolocationDotIoDriver::class;
    }
}