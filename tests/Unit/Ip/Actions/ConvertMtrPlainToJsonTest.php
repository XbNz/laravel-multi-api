<?php

declare(strict_types=1);

namespace XbNz\Resolver\Tests\Unit\Ip\Actions;

use Illuminate\Support\Collection;
use XbNz\Resolver\Domain\Ip\Actions\ConvertMtrPlainToJsonAction;
use XbNz\Resolver\Factories\Ip\RekindledMtrDotShFactory;

class ConvertMtrPlainToJsonTest extends \XbNz\Resolver\Tests\TestCase
{
    /** @test **/
    public function it_takes_a_plaintext_mtr_result_and_returns_valid_json(): void
    {
        // Arrange
        $action = app(ConvertMtrPlainToJsonAction::class);

        // Act
        $json = $action->execute(
            RekindledMtrDotShFactory::generateTestData(
                [
                    'plain_text' => '                                                       Loss% Drop   Rcv   Snt  Last  Best   Avg  Wrst StDev Gmean Jttr Javg Jmax Jint
  1.|-- po-25.lag.iad03.us.misaka.io (23.143.176.4)    30.0%    3     7    10   0.2   0.1   0.2   0.2   0.0   0.2  0.0  0.0  0.1  0.2
  2.|-- v204.cr01.iad03.us.misaka.io (23.143.176.2)     0.0%    0    10    10   0.3   0.2   0.3   0.3   0.0   0.3  0.0  0.0  0.1  0.3
  3.|-- v204.cr02.iad03.us.misaka.io (23.143.176.3)     0.0%    0    10    10   0.3   0.2   0.3   0.4   0.1   0.3  0.0  0.1  0.1  0.4
  4.|-- e2-2.cr01.iad01.us.misaka.io (23.143.176.44)    0.0%    0    10    10   0.5   0.5   0.6   0.7   0.0   0.6  0.1  0.1  0.1  0.4
  5.|-- equinix-ashburn.woodynet.net (206.126.236.111)  0.0%    0    10    10   0.9   0.9   2.3   9.3   2.7   1.6  0.4  2.6  8.3 18.4
  6.|-- dns9.quad9.net (9.9.9.9)                        0.0%    0    10    10   0.6   0.6   0.7   0.7   0.0   0.7  0.0  0.0  0.1  0.3',
                ]
            )
        );

        // Assert

        $collection = Collection::make(json_decode($json, true, 512, JSON_THROW_ON_ERROR));

        $hosts = Collection::make($collection->get('hops'))->pluck('hop_host');

        $this->assertSame('po-25.lag.iad03.us.misaka.io | (23.143.176.4)', $hosts[0]);
        $this->assertSame('v204.cr01.iad03.us.misaka.io | (23.143.176.2)', $hosts[1]);
        $this->assertSame('v204.cr02.iad03.us.misaka.io | (23.143.176.3)', $hosts[2]);
        $this->assertSame('e2-2.cr01.iad01.us.misaka.io | (23.143.176.44)', $hosts[3]);
        $this->assertSame('equinix-ashburn.woodynet.net | (206.126.236.111)', $hosts[4]);
        $this->assertSame('dns9.quad9.net | (9.9.9.9)', $hosts[5]);
    }

    /** @test **/
    public function it_works_with_missing_hop_hosts(): void
    {
        // Arrange
        $action = app(ConvertMtrPlainToJsonAction::class);

        // Act
        $json = $action->execute(
            RekindledMtrDotShFactory::generateTestData(
                [
                    'plain_text' => '                                                       Loss% Drop   Rcv   Snt  Last  Best   Avg  Wrst StDev Gmean Jttr Javg Jmax Jint
  1.|-- po-25.lag.iad03.us.misaka.io (23.143.176.4)    20.0%    2     8    10   0.1   0.1   0.1   0.1   0.0   0.1  0.0  0.0  0.0  0.1
  2.|-- v204.cr01.iad03.us.misaka.io (23.143.176.2)     0.0%    0    10    10   0.3   0.2   0.3   0.4   0.0   0.3  0.1  0.1  0.1  0.4
  3.|-- v204.cr02.iad03.us.misaka.io (23.143.176.3)     0.0%    0    10    10   0.3   0.2   0.3   0.3   0.0   0.3  0.1  0.0  0.1  0.3
  4.|-- e2-2.cr01.iad01.us.misaka.io (23.143.176.44)    0.0%    0    10    10   0.5   0.5   0.5   0.6   0.0   0.5  0.0  0.0  0.1  0.2
  5.|-- v3518.cr01.iad01.us.misaka.io (45.134.214.100)  0.0%    0    10    10   0.5   0.5   0.5   0.6   0.0   0.5  0.0  0.0  0.1  0.3
  6.|-- 195.22.206.113                                  0.0%    0    10    10   0.6   0.6   4.5  23.2   6.9   2.2  0.0  3.4 20.4 23.0
  7.|-- ???                                            100.0   10     0    10   0.0   0.0   0.0   0.0   0.0   0.0  0.0  0.0  0.0  0.0
  8.|-- 195.22.214.79                                   0.0%    0    10    10 109.3 106.9 115.2 162.1  16.8 114.3  4.5 13.6 54.0 107.7
  9.|-- 89.221.39.113                                   0.0%    0    10    10 111.1 111.1 111.3 112.3   0.4 111.3  0.3  0.4  1.2  3.0
 10.|-- ???                                            100.0   10     0    10   0.0   0.0   0.0   0.0   0.0   0.0  0.0  0.0  0.0  0.0
 11.|-- ???                                            100.0   10     0    10   0.0   0.0   0.0   0.0   0.0   0.0  0.0  0.0  0.0  0.0', ]
            )
        );

        // Assert

        $collection = Collection::make(json_decode($json, true, 512, JSON_THROW_ON_ERROR));

        $hosts = Collection::make($collection->get('hops'))->pluck('hop_host');

        $this->assertCount(11, $collection->get('hops'));
        $this->assertStringContainsString('???', $collection->get('hops')[7]['hop_host']);
        $this->assertStringContainsString('???', $collection->get('hops')[10]['hop_host']);
        $this->assertStringContainsString('???', $collection->get('hops')[11]['hop_host']);
    }

    /** @test **/
    public function it_passes_the_ip_and_probe_info_to_the_json(): void
    {
        // Arrange
        $action = app(ConvertMtrPlainToJsonAction::class);

        // Act
        $json = $action->execute(
            RekindledMtrDotShFactory::generateTestData([
                'ip' => '2.2.2.2',
                'probe_id' => 'ddddd',
            ])
        );

        // Assert

        $collection = Collection::make(json_decode($json, true, 512, JSON_THROW_ON_ERROR));

        $this->assertSame('2.2.2.2', $collection->get('target_ip'));
        $this->assertSame('ddddd', $collection->get('probe_id'));
    }
}
