<?php

namespace XbNz\Resolver\ServiceProviders;

use XbNz\Resolver\Domain\Ip\Builders\IpBuilder;
use XbNz\Resolver\Domain\Ip\Drivers\AbuseIpDbDotComDriver;
use XbNz\Resolver\Domain\Ip\Drivers\IpApiDotComDriver;
use XbNz\Resolver\Domain\Ip\Drivers\IpDataDotCoDriver;
use XbNz\Resolver\Domain\Ip\Drivers\IpGeolocationDotIoDriver;
use XbNz\Resolver\Domain\Ip\Drivers\MtrDotShMtrDriver;
use XbNz\Resolver\Domain\Ip\Mappings\AbuseIpDbDotComMapper;
use XbNz\Resolver\Domain\Ip\Mappings\IpDataDotCoMapper;
use XbNz\Resolver\Domain\Ip\Mappings\IpGeolocationDotIoMapper;
use XbNz\Resolver\Domain\Ip\Mappings\MtrDotShMtrMapper;
use XbNz\Resolver\Factories\MappedResultFactory;


class IpServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/ip-resolver.php', 'ip-resolver');

        $this->app->tag([
            IpGeolocationDotIoDriver::class,
            IpDataDotCoDriver::class,
            MtrDotShMtrDriver::class,
            AbuseIpDbDotComDriver::class,
            IpApiDotComDriver::class,
        ], 'ip-drivers');

        $this->app->when(IpBuilder::class)->needs('$drivers')->giveTagged('ip-drivers');

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