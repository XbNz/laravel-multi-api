<?php

namespace XbNz\Resolver\Tests;

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
    }

    protected function setUp(): void
    {
        parent::setUp();
    }
}