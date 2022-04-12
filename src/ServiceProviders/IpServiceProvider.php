<?php

namespace XbNz\Resolver\ServiceProviders;

use XbNz\Resolver\Domain\Ip\Actions\FetchRawDataForIpsAction;
use XbNz\Resolver\Domain\Ip\Drivers\AbuseIpDbDotComDriver;
use XbNz\Resolver\Domain\Ip\Drivers\IpDataDotCoDriver;
use XbNz\Resolver\Domain\Ip\Drivers\IpGeolocationDotIoDriver;
use XbNz\Resolver\Domain\Ip\Drivers\MtrDotShMtrDriver;
use XbNz\Resolver\Domain\Ip\Mappings\AbuseIpDbDotComMapper;
use XbNz\Resolver\Domain\Ip\Mappings\IpDataDotCoMapper;
use XbNz\Resolver\Domain\Ip\Mappings\IpGeolocationDotIoMapper;
use XbNz\Resolver\Domain\Ip\Strategies\AuthStrategies\AbuseIpDbDotComStrategy as AbuseIpDbDotComAuthStrategy;
use XbNz\Resolver\Domain\Ip\Strategies\AuthStrategies\IpDataDotCoStrategy as IpDataDotCoAuthStrategy;
use XbNz\Resolver\Domain\Ip\Strategies\AuthStrategies\IpGeolocationDotIoStrategy as IpGeolocationDotIoAuthStrategy;
use XbNz\Resolver\Domain\Ip\Strategies\ResponseFormatterStratagies\MtrDotShMtrStrategy;
use XbNz\Resolver\Domain\Ip\Strategies\RetryStrategies\AbuseIpDbDotComStrategy as AbuseIpDbDotComRetryStrategy;
use XbNz\Resolver\Domain\Ip\Strategies\RetryStrategies\IpDataDotCoStrategy as IpDataDotCoRetryStrategy;
use XbNz\Resolver\Domain\Ip\Strategies\RetryStrategies\IpGeolocationDotIoStrategy as IpGeolocationDotIoRetryStrategy;
use XbNz\Resolver\Factories\Ip\GuzzleIpClientFactory;
use XbNz\Resolver\Factories\Ip\NormalizedIpResultsDataFactory;


class IpServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/ip-resolver.php', 'ip-resolver');

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
            IpGeolocationDotIoDriver::class,
            IpDataDotCoDriver::class,
            AbuseIpDbDotComDriver::class,
            MtrDotShMtrDriver::class,
        ], 'drivers');

        $this->app->tag([
            IpGeolocationDotIoMapper::class,
            IpDataDotCoMapper::class,
            AbuseIpDbDotComMapper::class,
        ], 'mappers');

        $this->app->tag([
            MtrDotShMtrStrategy::class
        ], 'response-formatters');

        $this->app->when(GuzzleIpClientFactory::class)
            ->needs('$authStrategies')
            ->giveTagged('auth-strategies');

        $this->app->when(GuzzleIpClientFactory::class)
            ->needs('$retryStrategies')
            ->giveTagged('retry-strategies');

        $this->app->when(GuzzleIpClientFactory::class)
            ->needs('$responseFormatters')
            ->giveTagged('response-formatters');

        $this->app->when(FetchRawDataForIpsAction::class)
            ->needs('$drivers')
            ->giveTagged('drivers');

        $this->app->when(NormalizedIpResultsDataFactory::class)
            ->needs('$mappers')
            ->giveTagged('mappers');

    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../config/ip-resolver.php' =>
                    config_path('ip-resolver.php')
            ], 'ip-resolver');
        }
    }
}