<?php

declare(strict_types=1);

namespace XbNz\Resolver\Domain\Ip\Strategies\RetryStrategies;

use GuzzleHttp\Psr7\Uri;
use Illuminate\Support\Facades\Config;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use XbNz\Resolver\Domain\Ip\Services\IpGeolocationDotIo\IpGeolocationDotIoService;
use XbNz\Resolver\Domain\Ip\Services\Service;
use XbNz\Resolver\Support\Actions\GetRandomApiKeyAction;
use XbNz\Resolver\Support\Guzzle\Middlewares\WithRetry;
use XbNz\Resolver\Support\Strategies\RetryStrategy;

class IpGeolocationDotIoStrategy implements RetryStrategy
{
    public function __construct(
        private readonly GetRandomApiKeyAction $getRandomApiKey,
        private readonly WithRetry $withRetry
    ) {
    }

    public function guzzleMiddleware(): callable
    {
        return ($this->withRetry)(
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
                $randomKey = $this->getRandomApiKey->execute(IpGeolocationDotIoService::class, 'ip-resolver.api-keys');

                $newUri = Uri::withQueryValue($uri, 'apiKey', $randomKey);
                $request = $request->withUri($newUri);
            }
        );
    }

    /**
     * @param class-string<IpGeolocationDotIoService> $service
     */
    public function supports(string $service): bool
    {
        return $service === IpGeolocationDotIoService::class;
    }
}
