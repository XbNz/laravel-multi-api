<?php

namespace XbNz\Resolver\Tests\Unit\Ip\Actions;

use XbNz\Resolver\Domain\Ip\Actions\VerifyIpIntegrityAction;
use XbNz\Resolver\Domain\Ip\Exceptions\InvalidIpAddressException;

class VerifyIpIntegrityTest extends \XbNz\Resolver\Tests\TestCase
{
    /** @test */
    public function it_takes_an_ipv4_and_returns_an_ip_data_dto()
    {
        $string = '1.1.1.1';

        $ipData = app(VerifyIpIntegrityAction::class)
            ->execute($string);

        $this->assertEquals(4, $ipData->version);
        $this->assertEquals('1.1.1.1', $ipData->ip);
    }

    /** @test */
    public function it_takes_an_ipv6_and_returns_an_ip_data_dto()
    {
        $string = '2001:4860:4860::8844';

        $ipData = app(VerifyIpIntegrityAction::class)
            ->execute($string);

        $this->assertEquals(6, $ipData->version);
        $this->assertEquals('2001:4860:4860::8844', $ipData->ip);
    }


    /** @test */
    public function rejects_internal_ip_ranges()
    {
        try {
            app(VerifyIpIntegrityAction::class)
                ->execute('192.168.1.1');
        } catch (InvalidIpAddressException $e) {
            $this->assertInstanceOf(InvalidIpAddressException::class, $e);
            return;
        }

        $this->fail('Did not throw expected Ip error');
    }

    /** @test */
    public function rejects_reserved_ip_ranges()
    {
        try {
            app(VerifyIpIntegrityAction::class)
                ->execute('64:ff9b:1::/48');
        } catch (InvalidIpAddressException $e) {
            $this->assertInstanceOf(InvalidIpAddressException::class, $e);
            return;
        }

        $this->fail('Did not throw expected Ip error');
    }
}