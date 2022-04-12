<?php

namespace XbNz\Resolver\Tests\Unit\Ip\Actions;

use XbNz\Resolver\Domain\Ip\Actions\FetchRawDataForIpsAction;
use XbNz\Resolver\Domain\Ip\Builders\DriverBuilder;
use XbNz\Resolver\Domain\Ip\Drivers\AbuseIpDbDotComDriver;
use XbNz\Resolver\Domain\Ip\Drivers\IpGeolocationDotIoDriver;
use XbNz\Resolver\Domain\Ip\Drivers\MtrDotShMtrDriver;
use XbNz\Resolver\Domain\Ip\DTOs\IpData;

class FetchRawDataForIpsTest extends \XbNz\Resolver\Tests\TestCase
{
    /** @test **/
    public function example(): void
    {
        // Arrange
        $fetcher = app(FetchRawDataForIpsAction::class);

        // Act
        $data = $fetcher->execute(
            [
                new IpData('1.1.1.1', 4),
            ],
            [MtrDotShMtrDriver::class]
        );

        dd($data);


        // Assert
    }
}