<?php

namespace XbNz\Resolver\ServiceProviders;

use XbNz\Resolver\Domain\Ip\Factories\IpConfigFactory;
use XbNz\Resolver\Domain\Ip\Strategies\AuthStrategies\IpGeolocationDotIoStrategy as IpGeolocationDotIoAuthStrategy;
use XbNz\Resolver\Domain\Ip\Strategies\SoloIpAddressStrategies\IpGeolocationDotIoStrategy as IpGeolocationDotIoSoloIpStrategy;
use XbNz\Resolver\Domain\Ip\Strategies\RetryStrategies\IpGeolocationDotIoStrategy as IpGeolocationDotIoRetryStrategy;



class IpServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/ip-resolver.php', 'ip-resolver');

        $this->app->tag([
            IpGeolocationDotIoSoloIpStrategy::class,
        ], 'solo-ip-strategies');

        $this->app->tag([
            IpGeolocationDotIoAuthStrategy::class,
        ], 'auth-strategies');

        $this->app->tag([
            IpGeolocationDotIoRetryStrategy::class,
        ], 'retry-strategies');

        $this->app->when(IpConfigFactory::class)
            ->needs('$soloIpStrategies')
            ->giveTagged('solo-ip-strategies');

        $this->app->when(IpConfigFactory::class)
            ->needs('$authStrategies')
            ->giveTagged('auth-strategies');

        $this->app->when(IpConfigFactory::class)
            ->needs('$retryStrategies')
            ->giveTagged('retry-strategies');
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