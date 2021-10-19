<?php

namespace XbNz\Resolver\Tests\Feature\Ip\Drivers;

use XbNz\Resolver\Domain\Ip\Builders\DriverBuilder;
use XbNz\Resolver\Domain\Ip\Collections\IpCollection;
use XbNz\Resolver\Domain\Ip\Drivers\IpGeolocationDotIoDriver;
use XbNz\Resolver\Domain\Ip\Drivers\IpInfoDriverDotIoDriver;
use XbNz\Resolver\Resolver\Resolver;
use XbNz\Resolver\Support\Exceptions\DriverNotFoundException;

class IpGeolocationDotIoDriverTest extends \XbNz\Resolver\Tests\TestCase
{
    /** @test */
    public function it_resolves_and_caches_the_ip_information()
    {
        \Config::set('resolver.cache_period', 3600);
        $info = app(Resolver::class)
            ->ip()
            ->ipGeolocationDotIo()
            ->withIp('1.1.1.1')
            ->normalize();

        $this->assertInstanceOf(IpCollection::class, $info);
        $this->assertTrue(\Cache::has(IpGeolocationDotIoDriver::class . '1.1.1.1'));
    }

    /** @test */
    public function it_throws_an_exception_when_a_driver_is_not_supported()
    {
        $driverMock = $this->mock(IpGeolocationDotIoDriver::class);
        $driverMock->shouldReceive('supports')
            ->once()
            ->andReturn('definitely-not-ip-geolocation-dot-io');

        try {
            app(Resolver::class)
                ->ip()
                ->withIp('1.1.1.1')
                ->ipGeolocationDotIo();
        } catch (DriverNotFoundException $e) {
            $this->assertInstanceOf(DriverNotFoundException::class, $e);
            return;
        }
        $this->fail('Exception was not thrown even though the driver isn\'t supported');
    }
}