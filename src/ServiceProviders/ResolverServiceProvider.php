<?php

namespace XbNz\Resolver\ServiceProviders;

use XbNz\Resolver\Domain\Ip\Mappings\AbuseIpDbDotComMapper;
use XbNz\Resolver\Domain\Ip\Mappings\IpDataDotCoMapper;
use XbNz\Resolver\Domain\Ip\Mappings\IpGeolocationDotIoMapper;
use XbNz\Resolver\Domain\Ip\Mappings\MtrDotShMtrMapper;
use XbNz\Resolver\Domain\Ip\Strategies\AuthStrategies\AbuseIpDbDotComStrategy as AbuseIpDbDotComAuthStrategy;
use XbNz\Resolver\Domain\Ip\Strategies\AuthStrategies\IpDataDotCoStrategy as IpDataDotCoAuthStrategy;
use XbNz\Resolver\Domain\Ip\Strategies\AuthStrategies\IpGeolocationDotIoStrategy as IpGeolocationDotIoAuthStrategy;
use XbNz\Resolver\Domain\Ip\Strategies\ResponseFormatterStratagies\MtrDotShMtrStrategy;
use XbNz\Resolver\Domain\Ip\Strategies\RetryStrategies\AbuseIpDbDotComStrategy as AbuseIpDbDotComRetryStrategy;
use XbNz\Resolver\Domain\Ip\Strategies\RetryStrategies\IpDataDotCoStrategy as IpDataDotCoRetryStrategy;
use XbNz\Resolver\Domain\Ip\Strategies\RetryStrategies\IpGeolocationDotIoStrategy as IpGeolocationDotIoRetryStrategy;
use XbNz\Resolver\Factories\GuzzleClientFactory;
use XbNz\Resolver\Factories\MappedResultFactory;

class ResolverServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/resolver.php', 'resolver');


        $this->app->tag([
            IpGeolocationDotIoAuthStrategy::class,
            IpDataDotCoAuthStrategy::class,
            AbuseIpDbDotComAuthStrategy::class,
        ], 'auth-strategies');

        $this->app->tag([
            IpGeolocationDotIoRetryStrategy::class,
            IpDataDotCoRetryStrategy::class,
            AbuseIpDbDotComRetryStrategy::class,
        ], 'retry-strategies');

        $this->app->tag([
            MtrDotShMtrStrategy::class
        ], 'response-formatters');

        $this->app->tag([
            IpGeolocationDotIoMapper::class,
            IpDataDotCoMapper::class,
            AbuseIpDbDotComMapper::class,
            MtrDotShMtrMapper::class
        ], 'mappers');

        $this->app->when(GuzzleClientFactory::class)
            ->needs('$authStrategies')
            ->giveTagged('auth-strategies');

        $this->app->when(GuzzleClientFactory::class)
            ->needs('$retryStrategies')
            ->giveTagged('retry-strategies');

        $this->app->when(GuzzleClientFactory::class)
            ->needs('$responseFormatters')
            ->giveTagged('response-formatters');


        $this->app->when(MappedResultFactory::class)
            ->needs('$mappers')
            ->giveTagged('mappers');

    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../config/resolver.php' =>
                    config_path('resolver.php')
            ], 'resolver');
        }
    }
}