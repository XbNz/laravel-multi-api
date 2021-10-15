<?php

namespace XbNz\Resolver\Tests\Unit\Ip\Actions;

use XbNz\Resolver\Domain\Ip\Actions\GetApiKeyForDriverAction;
use XbNz\Resolver\Domain\Ip\Drivers\Driver;
use XbNz\Resolver\Domain\Ip\Drivers\IpInfoDriver;
use XbNz\Resolver\Support\Exceptions\ConfigNotFoundException;

class GetApiKeyForDriverTest extends \XbNz\Resolver\Tests\TestCase
{
    /** @test */
    public function it_fetches_the_key_for_the_given_driver()
    {
        \Config::set('ip-resolver.api-keys', [
            'ipInfo' => 'something-that-looks-like-an-api-key',
        ]);

        $driverMock = $this->createMock(Driver::class);
        $driverMock->method('supports')->willReturn('ipInfo');

        $key = app(GetApiKeyForDriverAction::class)
            ->execute($driverMock);

        $this->assertEquals(
            'something-that-looks-like-an-api-key',
            $key
        );
    }

    /** @test */
    public function it_throws_an_exception_if_config_key_is_not_set()
    {
        \Config::set('ip-resolver.api-keys', null);

        $driverMock = $this->createMock(Driver::class);
        $driverMock->method('supports')->willReturn('ipInfo');

        try {
            app(GetApiKeyForDriverAction::class)
                ->execute($driverMock);
        } catch (ConfigNotFoundException $e) {
            $this->assertInstanceOf(ConfigNotFoundException::class, $e);
            return;
        }

        $this->fail('Did not throw config error');
    }
}