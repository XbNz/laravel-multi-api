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
        $data = $fetcher->execute(
            [
                new IpData('9.9.9.9', 4),
            ],
            ['ipgeolocation.io']
        );

        dd($data);

        // Assert
    }
}