<?php

namespace XbNz\Resolver\ServiceProviders;

use XbNz\Resolver\Domain\Ip\Builders\DriverBuilder;
use XbNz\Resolver\Domain\Ip\Drivers\IpGeolocationDriver;
use XbNz\Resolver\Domain\Ip\Drivers\IpInfoDriver;

class ResolverServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/resolver.php', 'resolver');

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