<?php

declare(strict_types=1);

namespace XbNz\Resolver\Domain\Ip\Strategies\RetryStrategies;

use Illuminate\Support\Facades\Config;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\MtrDotToolsService;
use XbNz\Resolver\Domain\Ip\Services\Service;
use XbNz\Resolver\Support\Guzzle\Middlewares\WithRetry;
use XbNz\Resolver\Support\Strategies\RetryStrategy;

class MtrDotToolsStrategy implements RetryStrategy
{
    public function __construct(
        private readonly WithRetry $withRetry
    ) {
    }

    public function guzzleMiddleware(): callable
    {
        return ($this->withRetry)(
            Config::get('resolver.tries', 5),
            Config::get('resolver.retry_sleep', 2),
            Config::get('retry_sleep_multiplier', 1.5),
        );
    }

    /**
     * @param class-string<MtrDotToolsService> $service
     */
    public function supports(string $service): bool
    {
        return $service === MtrDotToolsService::class;
    }
}
