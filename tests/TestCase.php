<?php

namespace XbNz\Resolver\Tests;

use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;
use Illuminate\Support\Facades\Config;
use XbNz\Resolver\Domain\Ip\Drivers\AbuseIpDbDotComDriver;
use XbNz\Resolver\Domain\Ip\Drivers\IpApiDotComDriver;
use XbNz\Resolver\Domain\Ip\Drivers\IpDataDotCoDriver;
use XbNz\Resolver\Domain\Ip\Drivers\IpGeolocationDotIoDriver;
use XbNz\Resolver\Domain\Ip\Drivers\IpInfoDotIoDriver;
use XbNz\Resolver\Facades\ResolverFacade;
use XbNz\Resolver\ServiceProviders\IpServiceProvider;
use XbNz\Resolver\ServiceProviders\ResolverServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            IpServiceProvider::class,
            ResolverServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Resolver' => ResolverFacade::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app->useEnvironmentPath(__DIR__ . '/../');
        $app->bootstrapWith([LoadEnvironmentVariables::class]);
        parent::getEnvironmentSetUp($app);

        Config::set('ip-resolver.api-keys', [
            IpApiDotComDriver::class => [
                env('IP_API_DOT_COM_API_KEY')
            ],

            IpGeolocationDotIoDriver::class => [
                env('IP_GEOLOCATION_DOT_IO_API_KEY')
            ],

            IpInfoDotIoDriver::class => [
                env('IP_INFO_DOT_IO_API_KEY')
            ],

            IpDataDotCoDriver::class => [
                env('IP_DATA_DOT_CO_API_KEY')
            ],

            AbuseIpDbDotComDriver::class => [
                env('ABUSE_IP_DB_DOT_COM_API_KEY')
            ],
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();
    }
}