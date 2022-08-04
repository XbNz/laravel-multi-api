<?php

declare(strict_types=1);

namespace XbNz\Resolver\Tests\Feature\Ip\Services\AbstractApiDotCom;

use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Domain\Ip\Services\AbstractApiDotCom\AbstractApiDotComService;
use XbNz\Resolver\Domain\Ip\Services\AbstractApiDotCom\DTOs\AbstractApiGeolocationResultsData;
use XbNz\Resolver\Tests\TestCase;

class ServiceTest extends TestCase
{
    /** @test **/
    public function it_geolocates_an_ip(): void
    {
        // Arrange
        $service = app(AbstractApiDotComService::class);

        // Act
        $responses = $service->geolocate(
            [
                IpData::fromIp('92.184.105.98'),
                IpData::fromIp('9.9.9.9'),
            ],
        );

        // Assert
        $this->assertContainsOnlyInstancesOf(AbstractApiGeolocationResultsData::class, $responses);
    }
}
