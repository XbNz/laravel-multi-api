<?php

namespace XbNz\Resolver\Tests\Feature\Ip\Drivers;

use XbNz\Resolver\Domain\Ip\Builders\DriverBuilder;
use XbNz\Resolver\Domain\Ip\Collections\IpCollection;
use XbNz\Resolver\Domain\Ip\Drivers\IpGeolocationDotIoDriver;
use XbNz\Resolver\Domain\Ip\Drivers\IpInfoDotIoDriver;
use XbNz\Resolver\Factories\QueriedIpDataFactory;
use XbNz\Resolver\Resolver\Resolver;
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

        //TODO: Feed wrong API key, API doesn't throw expected 401.
        $this->assertInstanceOf(IpCollection::class, $info);
        $this->assertTrue(\Cache::has(IpInfoDotIoDriver::class . '1.1.1.1'));
    }

}