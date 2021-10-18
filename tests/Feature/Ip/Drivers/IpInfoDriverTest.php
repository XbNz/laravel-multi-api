<?php

namespace XbNz\Resolver\Tests\Feature\Ip\Drivers;

use XbNz\Resolver\Domain\Ip\Builders\DriverBuilder;
use XbNz\Resolver\Domain\Ip\Drivers\IpGeolocationDriver;
use XbNz\Resolver\Domain\Ip\Drivers\IpInfoDriver;
use XbNz\Resolver\Factories\QueriedIpDataFactory;
use XbNz\Resolver\Resolver\Resolver;
use XbNz\Resolver\Support\Exceptions\DriverNotFoundException;

class IpInfoDriverTest extends \XbNz\Resolver\Tests\TestCase
{
    /** @test */
    public function it_successfully_reaches_the_query_method_of_the_ip_info_driver()
    {
        $driverMock = $this->mock(IpInfoDriver::class);
        $driverMock->shouldReceive('supports')
            ->andReturn('ipInfo');
        $driverMock->shouldReceive('query')
            ->once()
            ->andReturn(QueriedIpDataFactory::generateTestData());

        app(Resolver::class)
            ->ip()
            ->ipInfo()
            ->execute('1.1.1.1')
            ->


//        app(Resolver::class)
//            ->ip()
//            ->ipInfo()
//            ->execute('1.1.1.1');


    }

    /** @test */
    public function it_throws_an_exception_when_a_driver_is_not_supported()
    {
        $driverMock = $this->mock(IpInfoDriver::class);
        $driverMock->shouldReceive('supports')
            ->once()
            ->andReturn('definitely-not-ip-info');

        try {
            app(Resolver::class)
                ->ip()
                ->ipInfo();
        } catch (DriverNotFoundException $e) {
            $this->assertInstanceOf(DriverNotFoundException::class, $e);
            return;
        }
        $this->fail('Exception was not thrown even though the driver isn\'t supported');
    }
}