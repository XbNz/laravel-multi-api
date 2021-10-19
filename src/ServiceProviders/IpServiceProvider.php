<?php

namespace XbNz\Resolver\ServiceProviders;

use XbNz\Resolver\Domain\Ip\Builders\DriverBuilder;
use XbNz\Resolver\Support\Drivers\Driver;
use XbNz\Resolver\Domain\Ip\Drivers\IpGeolocationDotIoDriver;
use XbNz\Resolver\Domain\Ip\Drivers\IpInfoDotIoDriver;

class IpServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/ip-resolver.php', 'ip-resolver');

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