<?php

namespace XbNz\Resolver\Tests\Feature;

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

        dd(
            $service->listProbes()
                ->online()
                ->canPerformMtrOn(IpVersion::FOUR)
                ->canPerformPingOn(IpVersion::FOUR)
                ->fuzzySearch('germany')
        );

        // Act

        // Assert
    }
}