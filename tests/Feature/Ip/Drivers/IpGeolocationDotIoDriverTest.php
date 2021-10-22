<?php

namespace XbNz\Resolver\Tests\Feature\Ip\Drivers;

use XbNz\Resolver\Domain\Ip\Builders\DriverBuilder;
use XbNz\Resolver\Domain\Ip\Collections\IpCollection;
use XbNz\Resolver\Domain\Ip\Drivers\IpGeolocationDotIoDriver;
use XbNz\Resolver\Domain\Ip\Drivers\IpInfoDotIoDriver;
use XbNz\Resolver\Resolver\Resolver;
use XbNz\Resolver\Support\Exceptions\DriverNotFoundException;

class IpGeolocationDotIoDriverTest extends \XbNz\Resolver\Tests\TestCase
{
    /** @test
     * @group Online
     */
    public function it_resolves_and_caches_the_ip_information()
    {
        \Config::set('resolver.cache_period', 3600);
        dump(env('IP_GEOLOCATION_DOT_IO_API_KEY'));
        $info = app(Resolver::class)
            ->ip()
            ->ipGeolocationDotIo()
            ->withIp('1.1.1.1')
            ->normalize();

        $this->assertInstanceOf(IpCollection::class, $info);
        $this->assertTrue(\Cache::has(IpGeolocationDotIoDriver::class . '1.1.1.1'));
    }

}