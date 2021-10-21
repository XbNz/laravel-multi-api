<?php

namespace XbNz\Resolver\Tests\Feature\Ip\Drivers;

use XbNz\Resolver\Domain\Ip\Collections\IpCollection;
use XbNz\Resolver\Domain\Ip\Drivers\IpApiDotComDriver;
use XbNz\Resolver\Domain\Ip\Drivers\IpGeolocationDotIoDriver;
use XbNz\Resolver\Resolver\Resolver;

class IpApiDotComDriverTest extends \XbNz\Resolver\Tests\TestCase
{
    /** @test
     * @group Online
     */
    public function it_resolves_and_caches_the_ip_information()
    {
        \Config::set('resolver.cache_period', 3600);
        $info = app(Resolver::class)
            ->ip()
            ->ipApiDotCom()
            ->withIp('1.1.1.1')
            ->normalize();

        $this->assertInstanceOf(IpCollection::class, $info);
        $this->assertTrue(\Cache::has(IpApiDotComDriver::class . '1.1.1.1'));
    }
}