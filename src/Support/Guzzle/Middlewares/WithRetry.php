<?php

declare(strict_types=1);

namespace XbNz\Resolver\Support\Guzzle\Middlewares;

use GuzzleRetry\GuzzleRetryMiddleware;
use Illuminate\Support\Facades\Config;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use XbNz\Resolver\Support\Actions\GetRandomProxyAction;

class WithRetry
{
    public function __construct(
        private readonly GetRandomProxyAction $getRandomProxyAction
    ) {
    }

    /**
     * @param array<int> $retryStatusCodes
     */
    public function __invoke(
        int $tries,
        float $retrySleep,
        float $retryBackoffMultiplier,
        array $retryStatusCodes = [400, 401, 403, 408, 429, 500, 502, 503, 504],
        bool $retryOnTimeout = true,
        callable $runOnRetryCallable = null,
    ): callable {
        return GuzzleRetryMiddleware::factory([
            'max_retry_attempts' => $tries,
            'retry_on_status' => $retryStatusCodes,
            'max_allowable_timeout_secs' => $retrySleep,
            'retry_on_timeout' => $retryOnTimeout,
            'on_retry_callback' => function (
                int $attemptNumber,
                float $delay,
                RequestInterface &$request,
                array &$options,
                ?ResponseInterface $response
            ) use ($runOnRetryCallable) {
                if (Config::get('resolver.use_proxy', false) === true) {
                    $options['proxy'] = $this->getRandomProxyAction->execute();
                }

                if ($runOnRetryCallable !== null) {
                    $runOnRetryCallable($attemptNumber, $delay, $request, $options, $response);
                }
            },
            'default_retry_multiplier' => $retryBackoffMultiplier,
        ]);
    }
}
