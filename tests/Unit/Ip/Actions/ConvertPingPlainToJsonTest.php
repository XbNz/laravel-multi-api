<?php

declare(strict_types=1);

namespace XbNz\Resolver\Tests\Unit\Ip\Actions;

use Illuminate\Support\Collection;
use Webmozart\Assert\Assert;
use XbNz\Resolver\Domain\Ip\Actions\ConvertPingPlainToJsonAction;
use XbNz\Resolver\Factories\Ip\RekindledMtrDotShFactory;

class ConvertPingPlainToJsonTest extends \XbNz\Resolver\Tests\TestCase
{
    /** @test **/
    public function it_takes_a_plaintext_ping_result_and_returns_valid_json(): void
    {
        // Arrange
        $action = app(ConvertPingPlainToJsonAction::class);

        // Act
        $json = $action->execute(
            RekindledMtrDotShFactory::generateTestData(
                [
                    'plain_text' => 'PING 93.177.73.35 (93.177.73.35) 56(84) bytes of data.
64 bytes from 93.177.73.35: icmp_seq=1 ttl=54 time=242 ms
64 bytes from 93.177.73.35: icmp_seq=2 ttl=54 time=242 ms
64 bytes from 93.177.73.35: icmp_seq=3 ttl=54 time=243 ms
64 bytes from 93.177.73.35: icmp_seq=4 ttl=54 time=242 ms
64 bytes from 93.177.73.35: icmp_seq=5 ttl=54 time=242 ms

--- 93.177.73.35 ping statistics ---
10 packets transmitted, 5 received, 50% packet loss, time 807ms
rtt min/avg/max/mdev = 242.022/242.562/244.487/0.907 ms',
                ]
            )
        );

        $collection = Collection::make(json_decode($json, true, 512, JSON_THROW_ON_ERROR));

        // Assert

        $this->assertCount(5, $collection->get('sequences'));
        $this->assertSame(50, $collection->get('packet_loss'));

        $this->assertIsNumeric($collection->get('statistics')['minimum_rtt']);
        $this->assertIsNumeric($collection->get('statistics')['average_rtt']);
        $this->assertIsNumeric($collection->get('statistics')['maximum_rtt']);
        $this->assertIsNumeric($collection->get('statistics')['jitter']);

        foreach ($collection->get('sequences') as $sequence) {
            $this->assertIsNumeric($sequence['size']);
            $this->assertIsNumeric($sequence['rtt']);
            $this->assertIsNumeric($sequence['time_to_live']);
            $this->assertIsNumeric($sequence['sequence_number']);
            Assert::ip($sequence['ip']);
        }
    }

    /** @test **/
    public function it_works_with_100_percent_loss(): void
    {
        // Arrange
        $action = app(ConvertPingPlainToJsonAction::class);

        // Act
        $json = $action->execute(
            RekindledMtrDotShFactory::generateTestData(
                [
                    'plain_text' => 'PING 1.33.22.11 (1.33.22.11) 56(84) bytes of data.

--- 1.33.22.11 ping statistics ---
10 packets transmitted, 0 received, 100% packet loss, time 1832ms', ]
            )
        );

        $collection = Collection::make(json_decode($json, true, 512, JSON_THROW_ON_ERROR));

        // Assert
        $this->assertSame(100, $collection->get('packet_loss'));

        $this->assertNull($collection->get('statistics'));
        $this->assertNull($collection->get('sequences'));
    }

    /** @test **/
    public function it_passes_the_ip_and_probe_info_to_the_json(): void
    {
        // Arrange
        $action = app(ConvertPingPlainToJsonAction::class);

        // Act
        $json = $action->execute(
            RekindledMtrDotShFactory::generateTestData([
                'ip' => '2.2.2.2',
                'probe_id' => 'ddddd',
                'plain_text' => 'PING 1.33.22.11 (1.33.22.11) 56(84) bytes of data.

--- 1.33.22.11 ping statistics ---
10 packets transmitted, 0 received, 100% packet loss, time 1832ms',
            ])
        );

        // Assert

        $collection = Collection::make(json_decode($json, true, 512, JSON_THROW_ON_ERROR));

        $this->assertSame('2.2.2.2', $collection->get('target_ip'));
        $this->assertSame('ddddd', $collection->get('probe_id'));
    }
}
