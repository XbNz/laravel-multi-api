<?php

declare(strict_types=1);

namespace XbNz\Resolver\ServiceProviders;

use Illuminate\Foundation\Application;
use XbNz\Resolver\Domain\Ip\Services\IpGeolocationDotIo\IpGeolocationDotIoService;
use XbNz\Resolver\Domain\Ip\Services\IpGeolocationDotIo\Mappers\GeolocationMapper;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Mappers\ListAllProbesMapper;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Mappers\PerformMtrMapper;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Mappers\PerformPingMapper;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\MtrDotToolsService;
use XbNz\Resolver\Domain\Ip\Strategies\AuthStrategies\IpGeolocationDotIoStrategy as IpGeolocationDotIoAuthStrategy;
use XbNz\Resolver\Domain\Ip\Strategies\RetryStrategies\IpGeolocationDotIoStrategy as IpGeolocationDotIoRetryStrategy;
use XbNz\Resolver\Domain\Ip\Strategies\RetryStrategies\MtrDotToolsStrategy;
use XbNz\Resolver\Factories\GuzzleClientFactory;
use XbNz\Resolver\Factories\UniversalMiddlewaresFactory;

class ResolverServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/resolver.php', 'resolver');

        $this->app->tag([
            IpGeolocationDotIoAuthStrategy::class,
        ], 'auth-strategies');

        $this->app->tag([
            IpGeolocationDotIoRetryStrategy::class,
            MtrDotToolsStrategy::class,
        ], 'retry-strategies');

        $this->app->tag([
            //
        ], 'response-formatters');

        $this->app->bind(GuzzleClientFactory::class, static function (Application $app) {
            return new GuzzleClientFactory(
                $app->make(UniversalMiddlewaresFactory::class),
                iterator_to_array($app->tagged('auth-strategies')->getIterator()),
                iterator_to_array($app->tagged('retry-strategies')->getIterator()),
                iterator_to_array($app->tagged('response-formatters')->getIterator()),
            );
        });

        $this->app->bind(MtrDotToolsService::class, static function (Application $app) {
            return new MtrDotToolsService(
                $app->make(GuzzleClientFactory::class)->for(MtrDotToolsService::class),
                $app->make(ListAllProbesMapper::class),
                $app->make(PerformMtrMapper::class),
                $app->make(PerformPingMapper::class)
            );
        });

        $this->app->bind(IpGeolocationDotIoService::class, static function (Application $app) {
            return new IpGeolocationDotIoService(
                $app->make(GuzzleClientFactory::class)->for(IpGeolocationDotIoService::class),
                $app->make(GeolocationMapper::class),
            );
        });
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../config/resolver.php' =>
                    config_path('resolver.php'),
            ], 'resolver');
        }
    }
}
