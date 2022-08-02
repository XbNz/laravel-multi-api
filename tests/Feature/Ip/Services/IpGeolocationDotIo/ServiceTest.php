<?php

declare(strict_types=1);

namespace XbNz\Resolver\Tests\Feature\Ip\Services\IpGeolocationDotIo;

use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Domain\Ip\Services\IpGeolocationDotIo\IpGeolocationDotIoService;
use XbNz\Resolver\Tests\TestCase;

class ServiceTest extends TestCase
{
    /** @test
     * @group Online
     **/
    public function it_geolocates_an_ip(): void
    {
        // Arrange
        $service = app(IpGeolocationDotIoService::class);

        // Act
        $response = $service->geolocate(
            [IpData::fromIp('1.1.1.1')],
            function (array $responses) {
                dd($responses[0]->guzzleResponse->getBody()->getContents());
            }

            //TODO: Pick up here. Make a mapper for the dumped response.
        );

        // Assert
    }
}
