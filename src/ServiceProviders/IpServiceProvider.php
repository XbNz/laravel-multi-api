<?php

namespace XbNz\Resolver\ServiceProviders;

use XbNz\Resolver\Domain\Ip\Builders\DriverBuilder;
use XbNz\Resolver\Domain\Ip\Drivers\Driver;
use XbNz\Resolver\Domain\Ip\Drivers\IpGeolocationDriver;
use XbNz\Resolver\Domain\Ip\Drivers\IpInfoDriver;

class IpServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/ip-resolver.php', 'ip-resolver');

        $this->app->tag([
            IpInfoDriver::class,
            IpGeolocationDriver::class
        ], 'ip-drivers');

        $this
            ->app
            ->when(DriverBuilder::class)
            ->needs('$drivers')
            ->giveTagged('ip-drivers');
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