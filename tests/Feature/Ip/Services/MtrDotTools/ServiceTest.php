<?php

declare(strict_types=1);

namespace XbNz\Resolver\Tests\Feature\Ip\Services\MtrDotTools;

use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\DTOs\MtrDotToolsMtrResultsData;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\DTOs\MtrDotToolsPingResultsData;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\DTOs\MtrDotToolsProbeData;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Enums\IpVersion;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\MtrDotToolsService;
use XbNz\Resolver\Tests\TestCase;

class ServiceTest extends TestCase
{
    /** @test
     * @group Online
     **/
    public function it_gets_a_list_of_probes(): void
    {
        // Arrange
        $service = app(MtrDotToolsService::class);

        // Act
        $collection = $service->listProbes();

        // Assert
        $this->assertContainsOnlyInstancesOf(MtrDotToolsProbeData::class, $collection);
    }

    /** @test
     * @group Online
     **/
    public function it_performs_mtr(): void
    {
        // Arrange
        $service = app(MtrDotToolsService::class);

        // Act
        $collection = $service->mtr(
            [IpData::fromIp('1.1.1.1')],
            $service->listProbes()->online()->canPerformMtrOn(IpVersion::FOUR)->shuffle()->take(1),
        );

        // Assert
        $this->assertContainsOnlyInstancesOf(MtrDotToolsMtrResultsData::class, $collection);
    }

    /** @test
     * @group Online
     **/
    public function it_performs_ping(): void
    {
        // Arrange
        $service = app(MtrDotToolsService::class);

        // Act
        $collection = $service->ping(
            [IpData::fromIp('1.1.1.1')],
            $service->listProbes()->online()->canPerformPingOn(IpVersion::FOUR)->take(1),
        );

        // Assert
        $this->assertContainsOnlyInstancesOf(MtrDotToolsPingResultsData::class, $collection);
    }
}
