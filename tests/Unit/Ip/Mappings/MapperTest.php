<?php

namespace XbNz\Resolver\Tests\Unit\Ip\Mappings;

use XbNz\Resolver\Domain\Ip\Mappings\AbuseIpDbDotComMapper;
use XbNz\Resolver\Domain\Ip\Mappings\IpApiDotComMapper;
use XbNz\Resolver\Domain\Ip\Mappings\IpDataDotCoMapper;
use XbNz\Resolver\Domain\Ip\Mappings\IpGeolocationDotIoMapper;
use XbNz\Resolver\Domain\Ip\Mappings\IpInfoDotIoMapper;
use XbNz\Resolver\Domain\Ip\Mappings\MtrDotShMtrMapper;
use XbNz\Resolver\Domain\Ip\Mappings\MtrDotShPingMapper;
use XbNz\Resolver\Factories\RawResultsFactory;
use XbNz\Resolver\Support\DTOs\MappableDTO;
use XbNz\Resolver\Tests\TestCase;

class MapperTest extends TestCase
{
    /** @test **/
    public function it_returns_a_mappable_dto(): void
    {
        // Arrange
        $dataProvider = [
            AbuseIpDbDotComMapper::class => RawResultsFactory::abuseIpDbDotComFake(),
            IpApiDotComMapper::class => RawResultsFactory::ipApiDotComFake(),
            IpDataDotCoMapper::class => RawResultsFactory::ipDataDotCoFake(),
            IpGeolocationDotIoMapper::class => RawResultsFactory::ipGeolocationDotIoFake(),
            IpInfoDotIoMapper::class => RawResultsFactory::ipInfoDotIoFake(),
            MtrDotShMtrMapper::class => RawResultsFactory::mtrDotShMtrFake(),
            MtrDotShPingMapper::class => RawResultsFactory::mtrDotShPingFake(),
        ];

        // Act

        foreach ($dataProvider as $mapperClass => $rawResults) {
            $mapper = app($mapperClass);
            $dto = $mapper->map($rawResults);

            $this->assertInstanceOf(MappableDTO::class, $dto);
        }

        // Assert
    }
}