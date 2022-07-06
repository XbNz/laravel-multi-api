<?php

namespace XbNz\Resolver\Tests\Feature;

use GuzzleHttp\Psr7\Response;
use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Collections\ProbesCollection;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Enums\IpVersion;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\MtrDotToolsService;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Requests\ListAllProbes\ListAllProbesRequest;
use XbNz\Resolver\Support\Actions\FetchRawDataAction;
use XbNz\Resolver\Tests\TestCase;

class ExampleTest extends TestCase
{
    /** @test **/
    public function example(): void
    {
        // Arrange
        $service = app(MtrDotToolsService::class);

        $probes = $service->listProbes()->canPerformMtrOn(IpVersion::FOUR)->online();


        dd($service->mtr([IpData::fromIp('1.1.1.1')], $probes));

        // Act

        // Assert
    }
}