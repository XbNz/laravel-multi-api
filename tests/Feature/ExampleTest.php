<?php

namespace XbNz\Resolver\Tests\Feature;

use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Enums\IpVersion;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\MtrDotToolsService;
use XbNz\Resolver\Tests\TestCase;

class ExampleTest extends TestCase
{
    /** @test **/
    public function example(): void
    {
        // Arrange
        $service = app(MtrDotToolsService::class);

        $probes = $service->listProbes()->canPerformPingOn(IpVersion::FOUR)->online()->fuzzySearch('netherlands');



        dd($service->ping([IpData::fromIp('1.1.1.1')], $probes));

        // Act

        // Assert
    }
}