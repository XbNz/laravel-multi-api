<?php

namespace XbNz\Resolver\Tests\Feature\Ip\Services\MtrDotTools;


use GuzzleHttp\Client;
use Mockery;
use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Collections\ProbesCollection;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\DTOs\MtrDotToolsMtrResultsData;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\DTOs\MtrDotToolsPingResultsData;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\DTOs\MtrDotToolsProbeData;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Enums\IpVersion;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\MtrDotToolsService;
use XbNz\Resolver\Factories\GuzzleClientFactory;
use XbNz\Resolver\Support\DTOs\RequestResponseWrapper;
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
        $ran = 0;
        $collection = $service->mtr(
            [IpData::fromIp('1.1.1.1')],
            $service->listProbes()->online()->canPerformMtrOn(IpVersion::FOUR)->take(2),
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
            $service->listProbes()->online()->canPerformPingOn(IpVersion::FOUR)->take(2),
        );


        // Assert
        $this->assertContainsOnlyInstancesOf(MtrDotToolsPingResultsData::class, $collection);
    }
}