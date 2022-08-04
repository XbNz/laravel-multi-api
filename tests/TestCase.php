<?php

declare(strict_types=1);

namespace XbNz\Resolver\Tests;

use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;
use Illuminate\Support\Facades\Config;
use XbNz\Resolver\Domain\Ip\Services\AbstractApiDotCom\AbstractApiDotComService;
use XbNz\Resolver\Domain\Ip\Services\IpGeolocationDotIo\IpGeolocationDotIoService;
use XbNz\Resolver\Facades\ResolverFacade;
use XbNz\Resolver\ServiceProviders\IpServiceProvider;
use XbNz\Resolver\ServiceProviders\ResolverServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

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
            IpGeolocationDotIoService::class => [
                env('IP_GEOLOCATION_DOT_IO_API_KEY'),
            ],
            AbstractApiDotComService::class => [
                env('ABSTRACTAPI_DOT_COM_GEOLOCATION_API_KEY'),
            ],
        ]);
    }
}
