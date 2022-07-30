<?php

namespace XbNz\Resolver\Tests\Unit\Ip\Services\MtrDotTools;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\DTOs\MtrDotToolsHopData;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\DTOs\MtrDotToolsMtrResultsData;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\DTOs\MtrDotToolsPingResultsData;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\DTOs\MtrDotToolsPingStatisticsResultsData;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\DTOs\MtrDotToolsProbeData;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Enums\MTR;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Enums\Ping;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\MtrDotToolsService;
use XbNz\Resolver\Factories\GuzzleClientFactory;
use XbNz\Resolver\Support\DTOs\RequestResponseWrapper;
use XbNz\Resolver\Tests\TestCase;

class MapperTest extends TestCase
{

    /** @test **/
    public function the_probe_data_is_mapped_correctly(): void
    {
        // Arrange
        $clientFactoryMock = $this->mock(GuzzleClientFactory::class);

        $mockHandler = new MockHandler([
            new Response(200, [
                'Content-Type' => 'application/json',
            ], json_encode($this->listProbeSampleData())),
        ]);

        $clientFactoryMock->shouldReceive('for')
            ->once()
            ->with(MtrDotToolsService::class)
            ->andReturn(new Client(['handler' => $mockHandler]));

        $service = app(MtrDotToolsService::class);


        // Act
        $ran = 0;
        $collection = $service->listProbes(static function (RequestResponseWrapper $raw) use (&$ran): void {
            $ran++;
        });

        // Assert

        $this->assertEquals(1, $ran);
        $this->assertContainsOnlyInstancesOf(MtrDotToolsProbeData::class, $collection);

        $this->assertSame($collection->first()->probeId, 'FFgH5');

        $this->assertSame($collection->first()->mtr, MTR::OnIpVersion4);
        $this->assertSame($collection->first()->ping, Ping::OnIpVersion4);

        $this->assertSame($collection[1]->mtr, MTR::OnIpVersion6);
        $this->assertSame($collection[1]->ping, Ping::OnIpVersion6);

        $this->assertSame($collection[2]->mtr, MTR::OnBothIpVersions);
        $this->assertSame($collection[2]->ping, Ping::OnBothIpVersions);

        $this->assertSame($collection[3]->mtr, MTR::Incapable);
        $this->assertSame($collection[3]->ping, Ping::Incapable);

        $this->assertSame($collection->first()->asNumber, 138322);
        $this->assertSame($collection->first()->city, 'Fujairah');
        $this->assertSame($collection->first()->country, 'United Arab Emirates');
        $this->assertSame($collection->first()->continent, 'Middle East');
        $this->assertSame($collection->first()->provider, 'Afghan Wireless');
        $this->assertSame($collection->first()->providerUrl, 'https://afghan-wireless.com/');
        $this->assertSame($collection->first()->unLoCode, 'AE-FUJ');
        $this->assertTrue($collection->first()->isOnline);
        $this->assertFalse($collection->first()->residential);
    }

    /** @test **/
    public function the_mtr_data_is_mapped_correctly(): void
    {
        // Arrange
        $clientFactoryMock = $this->mock(GuzzleClientFactory::class);

        $mockHandler = new MockHandler([
            new Response(200, [
                'Content-Type' => 'application/json',
            ], json_encode($this->listProbeSampleData())),
            new Response(200, [
                'Content-Type' => 'text/plain',
            ], $this->mtrSampleData()),
        ]);

        $clientFactoryMock->shouldReceive('for')
            ->once()
            ->with(MtrDotToolsService::class)
            ->andReturn(new Client(['handler' => $mockHandler]));

        $service = app(MtrDotToolsService::class);

        // Act
        $probes = $service->listProbes();
        $ran = 0;
        $collection = $service->mtr(
            [IpData::fromIp('1.1.1.1')],
            $probes->take(1),
            function (array $raw) use (&$ran): void {
                $this->assertContainsOnlyInstancesOf(RequestResponseWrapper::class, $raw);
                $ran++;
            }
        );

        // Assert

        $this->assertEquals(1, $ran);
        $this->assertContainsOnlyInstancesOf(MtrDotToolsMtrResultsData::class, $collection);

        $this->assertSame($collection->first()->probe->probeId, $probes->first()->probeId);
        $this->assertSame($collection->first()->targetIp->ip, '1.1.1.1');
        $this->assertSame($collection->first()->targetIp->version, 4);

        $this->assertContainsOnlyInstancesOf(MtrDotToolsHopData::class, $collection->first()->hops);

        $this->assertSame($collection->first()->hops[1]->hopPositionCount, 1);
        $this->assertSame($collection->first()->hops[1]->hopHost, '31.14.238.1');
        $this->assertSame($collection->first()->hops[1]->packetLossPercentage, 0.0);
        $this->assertSame($collection->first()->hops[1]->droppedPackets, 0);
        $this->assertSame($collection->first()->hops[1]->receivedPackets, 10);
        $this->assertSame($collection->first()->hops[1]->sentPackets, 10);
        $this->assertSame($collection->first()->hops[1]->lastRttValue, 0.3);
        $this->assertSame($collection->first()->hops[1]->lowestRttValue, 0.2);
        $this->assertSame($collection->first()->hops[1]->averageRttValue, 0.3);
        $this->assertSame($collection->first()->hops[1]->highestRttValue, 0.4);
        $this->assertSame($collection->first()->hops[1]->standardDeviation, 0.1);
        $this->assertSame($collection->first()->hops[1]->geometricMean, 0.2);
        $this->assertSame($collection->first()->hops[1]->jitter, 0.1);
        $this->assertSame($collection->first()->hops[1]->averageJitter, 0.1);
        $this->assertSame($collection->first()->hops[1]->maximumJitter, 0.1);
        $this->assertSame($collection->first()->hops[1]->interarrivalJitter, 0.5);

    }

    /** @test **/
    public function the_ping_data_is_mapped_correctly(): void
    {
        // Arrange
        $clientFactoryMock = $this->mock(GuzzleClientFactory::class);

        $mockHandler = new MockHandler([
            new Response(200, [
                'Content-Type' => 'application/json',
            ], json_encode($this->listProbeSampleData())),
            new Response(200, [
                'Content-Type' => 'text/plain',
            ], $this->pingSampleData()),
        ]);

        $clientFactoryMock->shouldReceive('for')
            ->once()
            ->with(MtrDotToolsService::class)
            ->andReturn(new Client(['handler' => $mockHandler]));

        $service = app(MtrDotToolsService::class);

        // Act
        $probes = $service->listProbes();
        $ran = 0;
        $collection = $service->ping(
            [IpData::fromIp('1.1.1.1')],
            $probes->take(1),
            function (array $raw) use (&$ran): void {
                $this->assertContainsOnlyInstancesOf(RequestResponseWrapper::class, $raw);
                $ran++;
            }
        );

        // Assert

        $this->assertEquals(1, $ran);
        $this->assertContainsOnlyInstancesOf(MtrDotToolsPingResultsData::class, $collection);

        $this->assertSame($collection->first()->probe->probeId, $probes->first()->probeId);
        $this->assertSame($collection->first()->targetIp->ip, '1.1.1.1');
        $this->assertSame($collection->first()->targetIp->version, 4);

        $this->assertSame($collection->first()->packetLossPercentage, 0.0);
        $this->assertSame($collection->first()->sequences[0]->size, 64);
        $this->assertSame($collection->first()->sequences[0]->ip, '1.1.1.1');
        $this->assertSame($collection->first()->sequences[0]->sequenceNumber, 1);
        $this->assertSame($collection->first()->sequences[0]->timeToLive, 60);
        $this->assertSame($collection->first()->sequences[0]->roundTripTime, 4.59);

        $this->assertInstanceOf(MtrDotToolsPingStatisticsResultsData::class, $collection->first()->statistics);
        $this->assertSame($collection->first()->statistics->minimumRoundTripTime, 4.44);
        $this->assertSame($collection->first()->statistics->averageRoundTripTime, 4.511);
        $this->assertSame($collection->first()->statistics->maximumRoundTripTime, 4.59);
        $this->assertSame($collection->first()->statistics->jitter, 0.071111111111111);
    }

    public function listProbeSampleData(array $extras = []): array
    {
        $json = <<<JSON
        {
            "FFgH5":
            {
                "country": "United Arab Emirates",
                "city": "Fujairah",
                "unlocode": "AE-FUJ",
                "provider": "Afghan Wireless",
                "asnumber": 138322,
                "residential": false,
                "group": "Middle East",
                "caps":
                {
                    "mtr": 4,
                    "trace": true,
                    "ping": 4
                },
                "status": true,
                "providerurl": "https://afghan-wireless.com/"
            },
            "awefg4":
            {
                "country": "United Arab Emirates",
                "city": "Fujairah",
                "unlocode": "AE-FUJ",
                "provider": "Afghan Wireless",
                "asnumber": 138322,
                "residential": false,
                "group": "Middle East",
                "caps":
                {
                    "mtr": 6,
                    "trace": true,
                    "ping": 6
                },
                "status": true,
                "providerurl": "https://afghan-wireless.com/"
            },
            "FWEfe":
            {
                "country": "United Arab Emirates",
                "city": "Fujairah",
                "unlocode": "AE-FUJ",
                "provider": "Afghan Wireless",
                "asnumber": 138322,
                "residential": false,
                "group": "Middle East",
                "caps":
                {
                    "mtr": true,
                    "trace": true,
                    "ping": true
                },
                "status": true,
                "providerurl": "https://afghan-wireless.com/"
            },
            "wefwef2":
            {
                "country": "United Arab Emirates",
                "city": "Fujairah",
                "unlocode": "AE-FUJ",
                "provider": "Afghan Wireless",
                "asnumber": 138322,
                "residential": false,
                "group": "Middle East",
                "caps":
                {
                    "mtr": false,
                    "trace": true,
                    "ping": false
                },
                "status": true,
                "providerurl": "https://afghan-wireless.com/"
            }
        }       
        JSON;

        return array_merge(json_decode($json, true, 512, JSON_THROW_ON_ERROR), $extras);
    }

    public function mtrSampleData(): string
    {
        return <<<MTR
                                                                 Loss% Drop   Rcv   Snt  Last  Best   Avg  Wrst StDev Gmean Jttr Javg Jmax Jint
          1.|-- 31.14.238.1                               0.0%    0    10    10   0.3   0.2   0.3   0.4   0.1   0.2  0.1  0.1  0.1  0.5
          2.|-- 185.111.185.44                            0.0%    0    10    10   0.4   0.4   0.6   1.5   0.4   0.6  0.0  0.3  1.1  2.3
          3.|-- de-cix-madrid.as13335.net (185.1.192.12)  0.0%    0    10    10   5.3   5.2  13.9  31.9  10.3  10.7 24.1 11.4 26.7 93.7
          4.|-- 172.70.56.3                               0.0%    0    10    10   5.3   5.2   6.8  19.8   4.6   6.1  0.4  1.6 14.5  9.8
          5.|-- one.one.one.one (1.1.1.1)                 0.0%    0    10    10   4.6   4.5   4.6   4.7   0.1   4.6  0.1  0.1  0.2  0.5
        MTR;
    }

    public function pingSampleData(): string
    {
        return '
                                            PING 1.1.1.1 (1.1.1.1) 56(84) bytes of data.
            64 bytes from 1.1.1.1: icmp_seq=1 ttl=60 time=4.59 ms
            64 bytes from 1.1.1.1: icmp_seq=2 ttl=60 time=4.51 ms
            64 bytes from 1.1.1.1: icmp_seq=3 ttl=60 time=4.53 ms
            64 bytes from 1.1.1.1: icmp_seq=4 ttl=60 time=4.49 ms
            64 bytes from 1.1.1.1: icmp_seq=5 ttl=60 time=4.56 ms
            64 bytes from 1.1.1.1: icmp_seq=6 ttl=60 time=4.44 ms
            64 bytes from 1.1.1.1: icmp_seq=7 ttl=60 time=4.57 ms
            64 bytes from 1.1.1.1: icmp_seq=8 ttl=60 time=4.44 ms
            64 bytes from 1.1.1.1: icmp_seq=9 ttl=60 time=4.49 ms
            64 bytes from 1.1.1.1: icmp_seq=10 ttl=60 time=4.49 ms
            
            --- 1.1.1.1 ping statistics ---
            10 packets transmitted, 10 received, 0% packet loss, time 1809ms
            rtt min/avg/max/mdev = 4.435/4.509/4.594/0.050 ms
        ';
    }
}