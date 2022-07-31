<?php

declare(strict_types=1);

namespace XbNz\Resolver\Tests\Unit\Ip\Services\MtrDotTools\Collections;

use Illuminate\Support\ItemNotFoundException;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Collections\ProbesCollection;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\DTOs\MtrDotToolsProbeData;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Enums\IpVersion;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Enums\MTR;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Enums\Ping;
use XbNz\Resolver\Tests\TestCase;

class ProbesCollectionTest extends TestCase
{
    /** @test **/
    public function it_filters_by_mtr_capacity(): void
    {
        // Arrange
        $dtoA = new MtrDotToolsProbeData(
            '::test::',
            MTR::OnIpVersion4,
            Ping::OnIpVersion4,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null
        );
        $dtoB = new MtrDotToolsProbeData(
            '::test::',
            MTR::OnIpVersion6,
            Ping::OnIpVersion6,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null
        );
        $dtoC = new MtrDotToolsProbeData(
            '::test::',
            MTR::OnBothIpVersions,
            Ping::OnBothIpVersions,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null
        );

        $collection = new ProbesCollection([$dtoA, $dtoB, $dtoC]);

        // Act

        $shouldBeDtoAandC = $collection->canPerformMtrOn(IpVersion::FOUR)->values();
        $shouldBeDtoBandC = $collection->canPerformMtrOn(IpVersion::SIX)->values();
        $shouldBeDtoC = $collection->canPerformMtrOn(IpVersion::FOUR)->canPerformMtrOn(IpVersion::SIX)->values();

        // Assert

        $this->assertSame($dtoA, $shouldBeDtoAandC[0]);
        $this->assertSame($dtoC, $shouldBeDtoAandC[1]);

        $this->assertSame($dtoB, $shouldBeDtoBandC[0]);
        $this->assertSame($dtoC, $shouldBeDtoBandC[1]);

        $this->assertSame($dtoC, $shouldBeDtoC->sole());
    }

    /** @test **/
    public function it_filters_by_ping_capacity(): void
    {
        // Arrange
        $dtoA = new MtrDotToolsProbeData(
            '::test::',
            MTR::OnIpVersion4,
            Ping::OnIpVersion4,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null
        );
        $dtoB = new MtrDotToolsProbeData(
            '::test::',
            MTR::OnIpVersion6,
            Ping::OnIpVersion6,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null
        );
        $dtoC = new MtrDotToolsProbeData(
            '::test::',
            MTR::OnBothIpVersions,
            Ping::OnBothIpVersions,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null
        );

        $collection = new ProbesCollection([$dtoA, $dtoB, $dtoC]);

        // Act

        $shouldBeDtoAandC = $collection->canPerformPingOn(IpVersion::FOUR)->values();
        $shouldBeDtoBandC = $collection->canPerformPingOn(IpVersion::SIX)->values();
        $shouldBeDtoC = $collection->canPerformPingOn(IpVersion::FOUR)->canPerformPingOn(IpVersion::SIX)->values();

        // Assert

        $this->assertSame($dtoA, $shouldBeDtoAandC[0]);
        $this->assertSame($dtoC, $shouldBeDtoAandC[1]);

        $this->assertSame($dtoB, $shouldBeDtoBandC[0]);
        $this->assertSame($dtoC, $shouldBeDtoBandC[1]);

        $this->assertSame($dtoC, $shouldBeDtoC->sole());
    }

    /** @test **/
    public function it_filters_by_online_probes(): void
    {
        // Arrange
        $dtoA = new MtrDotToolsProbeData(
            '::test::',
            MTR::OnIpVersion4,
            Ping::OnIpVersion4,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            true,
            null
        );
        $dtoB = new MtrDotToolsProbeData(
            '::test::',
            MTR::OnIpVersion6,
            Ping::OnIpVersion6,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            false,
            null
        );
        $dtoC = new MtrDotToolsProbeData(
            '::test::',
            MTR::OnIpVersion6,
            Ping::OnIpVersion6,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            false,
            null
        );

        $collection = new ProbesCollection([$dtoA, $dtoB, $dtoC]);

        // Act

        $shouldBeDtoA = $collection->online(true)->values();
        $shouldBeDtoBandC = $collection->online(false)->values();

        // Assert

        $this->assertSame($dtoA, $shouldBeDtoA->sole());
        $this->assertSame($dtoB, $shouldBeDtoBandC[0]);
        $this->assertSame($dtoC, $shouldBeDtoBandC[1]);
    }

    /** @test **/
    public function it_filters_by_id(): void
    {
        // Arrange
        $dtoA = new MtrDotToolsProbeData(
            'very-unique-id',
            MTR::OnIpVersion4,
            Ping::OnIpVersion4,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null
        );
        $dtoB = new MtrDotToolsProbeData(
            'test',
            MTR::OnIpVersion6,
            Ping::OnIpVersion6,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null
        );

        $collection = new ProbesCollection([$dtoA, $dtoB]);

        // Act & Assert
        $shouldBeDtoA = $collection->findById('very-unique-id');

        $this->assertSame($dtoA, $shouldBeDtoA);

        $this->expectException(ItemNotFoundException::class);
        $collection->findById('complete-gibberish');
    }
}
