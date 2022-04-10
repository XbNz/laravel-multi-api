<?php

namespace XbNz\Resolver\Tests\Feature\Ip\Drivers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use XbNz\Resolver\Domain\Ip\Builders\DriverBuilder;
use XbNz\Resolver\Domain\Ip\Collections\IpCollection;
use XbNz\Resolver\Domain\Ip\Drivers\IpGeolocationDotIoDriver;
use XbNz\Resolver\Domain\Ip\Drivers\IpInfoDotIoDriver;
use XbNz\Resolver\Factories\NormalizedIpResultsDataFactory;
use XbNz\Resolver\Resolver\Resolver;
use XbNz\Resolver\Support\Exceptions\ApiProviderException;
use XbNz\Resolver\Support\Exceptions\DriverNotFoundException;

class IpInfoDotIoDriverTest extends \XbNz\Resolver\Tests\TestCase
{
    /** @test
     * @group Online
     */
    public function it_resolves_and_caches_the_ip_information()
    {
        \Config::set('resolver.cache_period', 3600);

        $info = app(Resolver::class)
            ->ip()
            ->ipInfoDotIo()
            ->withIp('1.1.1.1')
            ->normalize();

        $this->assertInstanceOf(IpCollection::class, $info);
        $this->assertTrue(\Cache::has(IpInfoDotIoDriver::class . '1.1.1.1'));
    }


    /** @test
     * @group Online
     */
    public function provided_an_incorrect_api_key_it_throws_the_expected_exception(): void
    {
        Cache::flush();
        Config::set('ip-resolver.api-keys.ipInfoDotIo', ['wrong-api-key-should-be-refused']);

        $this->expectException(ApiProviderException::class);

        app(Resolver::class)
            ->ip()
            ->ipInfoDotIo()
            ->withIp('1.1.1.1')
            ->normalize();
    }

}