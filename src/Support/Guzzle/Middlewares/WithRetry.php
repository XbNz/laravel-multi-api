<?php

declare(strict_types=1);

namespace XbNz\Resolver\Support\Guzzle\Middlewares;

use GuzzleRetry\GuzzleRetryMiddleware;

class WithRetry
{
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
            'on_retry_callback' => $runOnRetryCallable,
            'default_retry_multiplier' => $retryBackoffMultiplier,
        ]);
    }
}
