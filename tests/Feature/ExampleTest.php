<?php

namespace XbNz\Resolver\Tests\Feature;

use GuzzleHttp\Psr7\Response;
use XbNz\Resolver\Domain\Ip\DTOs\IpData;
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

        $service->mtr('qLYEb', [3 => new IpData('1.1.1.1', 4)]);

        // Act

        // Assert
    }
}