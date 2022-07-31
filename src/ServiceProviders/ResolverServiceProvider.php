<?php

declare(strict_types=1);

namespace XbNz\Resolver\ServiceProviders;

use Illuminate\Foundation\Application;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Mappers\ListAllProbesMapper;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Mappers\PerformMtrMapper;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Mappers\PerformPingMapper;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\MtrDotToolsService;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Requests\ListAllProbesRequest;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Requests\PerformMtrRequest;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Requests\PerformPingRequest;
use XbNz\Resolver\Domain\Ip\Strategies\AuthStrategies\AbstractApiDotComStrategy as AbstractApiDotComAuthStrategy;
use XbNz\Resolver\Domain\Ip\Strategies\AuthStrategies\AbuseIpDbDotComStrategy as AbuseIpDbDotComAuthStrategy;
use XbNz\Resolver\Domain\Ip\Strategies\AuthStrategies\IpApiDotComStrategy as IpApiDotComAuthStrategy;
use XbNz\Resolver\Domain\Ip\Strategies\AuthStrategies\IpDataDotCoStrategy as IpDataDotCoAuthStrategy;
use XbNz\Resolver\Domain\Ip\Strategies\AuthStrategies\IpGeolocationDotIoStrategy as IpGeolocationDotIoAuthStrategy;
use XbNz\Resolver\Domain\Ip\Strategies\AuthStrategies\IpInfoDotIoStrategy as IpInfoDotIoAuthStrategy;
use XbNz\Resolver\Domain\Ip\Strategies\ResponseFormatterStratagies\IpApiDotComStrategy as IpApiDotComFormatterStrategy;
use XbNz\Resolver\Domain\Ip\Strategies\RetryStrategies\AbstractApiDotComStrategy as AbstractApiDotComRetryStrategy;
use XbNz\Resolver\Domain\Ip\Strategies\RetryStrategies\AbuseIpDbDotComStrategy as AbuseIpDbDotComRetryStrategy;
use XbNz\Resolver\Domain\Ip\Strategies\RetryStrategies\IpApiDotComStrategy as IpApiDotComRetryStrategy;
use XbNz\Resolver\Domain\Ip\Strategies\RetryStrategies\IpApiDotCoStrategy as IpApiDotCoRetryStrategy;
use XbNz\Resolver\Domain\Ip\Strategies\RetryStrategies\IpDashApiDotComStrategy as IpDashApiDotComRetryStrategy;
use XbNz\Resolver\Domain\Ip\Strategies\RetryStrategies\IpDataDotCoStrategy as IpDataDotCoRetryStrategy;
use XbNz\Resolver\Domain\Ip\Strategies\RetryStrategies\IpGeolocationDotIoStrategy as IpGeolocationDotIoRetryStrategy;
use XbNz\Resolver\Domain\Ip\Strategies\RetryStrategies\MtrDotShMtrStrategy as MtrDotShMtrRetryStrategy;
use XbNz\Resolver\Domain\Ip\Strategies\RetryStrategies\MtrDotShPingStrategy as MtrDotShPingRetryStrategy;
use XbNz\Resolver\Factories\GuzzleClientFactory;
use XbNz\Resolver\Factories\UniversalMiddlewaresFactory;

class ResolverServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/resolver.php', 'resolver');

        $this->app->tag([
            //            IpGeolocationDotIoAuthStrategy::class,
            //            IpDataDotCoAuthStrategy::class,
            //            AbuseIpDbDotComAuthStrategy::class,
            //            IpApiDotComAuthStrategy::class,
            //            IpInfoDotIoAuthStrategy::class,
            //            AbstractApiDotComAuthStrategy::class,
        ], 'auth-strategies');

        $this->app->tag([
            //            IpGeolocationDotIoRetryStrategy::class,
            //            IpDataDotCoRetryStrategy::class,
            //            AbuseIpDbDotComRetryStrategy::class,
            //            IpApiDotComRetryStrategy::class,
            //            MtrDotShMtrRetryStrategy::class,
            //            MtrDotShPingRetryStrategy::class,
            //            IpDashApiDotComRetryStrategy::class,
            //            IpApiDotCoRetryStrategy::class,
            //            AbstractApiDotComRetryStrategy::class,
        ], 'retry-strategies');

        $this->app->tag([
            //            IpApiDotComFormatterStrategy::class,
        ], 'response-formatters');

        $this->app->tag([
            ListAllProbesMapper::class,
            PerformMtrMapper::class,
            PerformPingMapper::class,
        ], 'mappers');

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
                $app->make(ListAllProbesRequest::class),
                $app->make(ListAllProbesMapper::class),
                $app->make(PerformMtrRequest::class),
                $app->make(PerformMtrMapper::class),
                $app->make(PerformPingRequest::class),
                $app->make(PerformPingMapper::class)
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
