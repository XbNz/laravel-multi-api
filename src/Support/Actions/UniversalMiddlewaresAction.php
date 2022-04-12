<?php

declare(strict_types=1);

namespace XbNz\Resolver\Support\Actions;

use GuzzleHttp\HandlerStack;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Kevinrob\GuzzleCache\CacheMiddleware;
use Kevinrob\GuzzleCache\Storage\LaravelCacheStorage;
use Kevinrob\GuzzleCache\Strategy\GreedyCacheStrategy;
use XbNz\Resolver\Support\Guzzle\Middlewares\WithProxy;
use XbNz\Resolver\Support\Guzzle\Middlewares\WithTimeout;

class UniversalMiddlewaresAction
{
    private array $middlewares;

    public function __construct(
        private GetRandomProxyAction $randomProxy,
    ) {}

    /**
     * @return array<callable>
     */
    public function execute(): array
    {
        $this->addCache();

        if ($this->usingProxy()) {
            $this->addRandomProxy();
        }

        $this->addTimeout();

        return $this->middlewares;
    }

    private function usingProxy(): bool
    {
        return (bool) Config::get('resolver.use_proxy', false);
    }

    private function addTimeout(): void
    {
        $timeout = (float) Config::get('resolver.timeout', 5);

        $this->middlewares['timeout'] = (new WithTimeout)($timeout);
    }

    private function addRandomProxy(): void
    {
        $this->middlewares['proxy'] = (new WithProxy)($this->randomProxy->execute());
    }

    private function addCache(): void
    {
        $this->middlewares['caching'] = new CacheMiddleware(
            new GreedyCacheStrategy(
                new LaravelCacheStorage(
                    Cache::store()
                ), Config::get('resolver.cache_period', 3600)
            )
        );
    }
}