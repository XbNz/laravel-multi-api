<?php

namespace XbNz\Resolver\Tests\Unit\Ip\Actions;

use XbNz\Resolver\Domain\Ip\Actions\GetApiKeysForDriverAction;
use XbNz\Resolver\Support\Drivers\Driver;
use XbNz\Resolver\Domain\Ip\Drivers\IpInfoDriverDotIoDriver;
use XbNz\Resolver\Support\Exceptions\ConfigNotFoundException;

class GetApiKeysForDriverTest extends \XbNz\Resolver\Tests\TestCase
{
    /** @test */
    public function it_fetches_the_key_for_the_given_driver()
    {
        \Config::set('ip-resolver.api-keys', [
            'ipInfo' => ['something-that-looks-like-an-api-key'],
        ]);

        $driverMock = $this->createMock(Driver::class);
        $driverMock->method('supports')->willReturn('ipInfo');

        $keys = app(GetApiKeysForDriverAction::class)
            ->execute($driverMock);

        $this->assertEquals(
            ['something-that-looks-like-an-api-key'],
            $keys
        );
    }

    /** @test */
    public function it_throws_an_exception_if_config_key_is_not_set()
    {
        \Config::set('ip-resolver.api-keys', null);

        $driverMock = $this->createMock(Driver::class);
        $driverMock->method('supports')->willReturn('ipInfo');

        try {
            app(GetApiKeysForDriverAction::class)
                ->execute($driverMock);
        } catch (ConfigNotFoundException $e) {
            $this->assertInstanceOf(ConfigNotFoundException::class, $e);
            return;
        }

        $this->fail('Did not throw config error');
    }
}