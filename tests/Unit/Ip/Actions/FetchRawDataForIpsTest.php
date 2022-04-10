<?php

namespace XbNz\Resolver\Tests\Unit\Ip\Actions;

use XbNz\Resolver\Domain\Ip\Actions\FetchRawDataForIpsAction;
use XbNz\Resolver\Domain\Ip\Builders\DriverBuilder;
use XbNz\Resolver\Domain\Ip\DTOs\IpData;

class FetchRawDataForIpsTest extends \XbNz\Resolver\Tests\TestCase
{
    /** @test **/
    public function example(): void
    {
        // Arrange
//        $fetcher = app(FetchRawDataForIpsAction::class);

        // Act
//        $data = $fetcher->execute(
//            [
//                new IpData('2606:4700:4700::1111', 6),
//            ],
//            ['abuseipdb.com', 'ipgeolocation.io', 'ipdata.co']
//        );

        $test = app(DriverBuilder::class);

        dd($test->ipGeolocationDotIo()
            ->ipDataDotCo()
            ->abuseIpDbDotCom()
            ->withIps(['9.9.9.9'])
            ->normalize());


        // Assert
    }
}