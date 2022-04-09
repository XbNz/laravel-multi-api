<?php

namespace XbNz\Resolver\Tests\Unit\Ip\Actions;

use XbNz\Resolver\Domain\Ip\Actions\FetchRawDataForIpsAction;
use XbNz\Resolver\Domain\Ip\DTOs\IpData;

class FetchRawDataForIpsTest extends \XbNz\Resolver\Tests\TestCase
{
    /** @test **/
    public function example(): void
    {
        // Arrange
        $fetcher = app(FetchRawDataForIpsAction::class);

        // Act
        $fetcher->execute(
            [
                new IpData('9.9.9.9', 4),
                new IpData('1.0.0.1', 4),
                new IpData('8.8.8.8', 4),
                new IpData('8.8.4.4', 4),
                new IpData('5.5.5.5', 4),
                new IpData('4.2.2.4', 4),
            ],
            ['ipgeolocation.io']
        );

        // Assert
    }
}