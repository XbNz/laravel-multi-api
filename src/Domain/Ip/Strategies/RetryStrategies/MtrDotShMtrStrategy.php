<?php

declare(strict_types=1);

namespace XbNz\Resolver\Domain\Ip\Strategies\RetryStrategies;

use Illuminate\Support\Facades\Config;
use XbNz\Resolver\Domain\Ip\Drivers\MtrDotShMtrDriver;
use XbNz\Resolver\Support\Guzzle\Middlewares\WithRetry;
use XbNz\Resolver\Support\Strategies\RetryStrategy;

class MtrDotShMtrStrategy implements RetryStrategy
{
    public function guzzleMiddleware(): callable
    {
        return (new WithRetry())(
            Config::get('resolver.tries', 5),
            Config::get('resolver.retry_sleep', 2),
            Config::get('retry_sleep_multiplier', 1.5),
        );
    }

    public function supports(string $driver): bool
    {
        return $driver === MtrDotShMtrDriver::class;
    }
}
