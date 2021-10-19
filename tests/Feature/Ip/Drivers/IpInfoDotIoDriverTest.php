<?php

namespace XbNz\Resolver\Tests\Feature\Ip\Drivers;

use XbNz\Resolver\Domain\Ip\Builders\DriverBuilder;
use XbNz\Resolver\Domain\Ip\Collections\IpCollection;
use XbNz\Resolver\Domain\Ip\Drivers\IpGeolocationDotIoDriver;
use XbNz\Resolver\Domain\Ip\Drivers\IpInfoDriverDotIoDriver;
use XbNz\Resolver\Factories\QueriedIpDataFactory;
use XbNz\Resolver\Resolver\Resolver;
use XbNz\Resolver\Support\Exceptions\DriverNotFoundException;

class IpInfoDotIoDriverTest extends \XbNz\Resolver\Tests\TestCase
{
    /** @test */
    public function it_resolves_and_caches_the_ip_information()
    {
        \Config::set('resolver.cache_period', 3600);
        $info = app(Resolver::class)
            ->ip()
            ->ipInfoDotIo()
            ->withIp('1.1.1.1')
            ->normalize();

        dd($info);
        $this->assertInstanceOf(IpCollection::class, $info);
        $this->assertTrue(\Cache::has(IpInfoDriverDotIoDriver::class . '1.1.1.1'));
    }

    /** @test */
    public function it_throws_an_exception_when_a_driver_is_not_supported()
    {
        $driverMock = $this->mock(IpInfoDriverDotIoDriver::class);
        $driverMock->shouldReceive('supports')
            ->once()
            ->andReturn('definitely-not-ip-info');

        try {
            app(Resolver::class)
                ->ip()
                ->withIp('1.1.1.1')
                ->ipInfo();
        } catch (DriverNotFoundException $e) {
            $this->assertInstanceOf(DriverNotFoundException::class, $e);
            return;
        }
        $this->fail('Exception was not thrown even though the driver isn\'t supported');
    }
}