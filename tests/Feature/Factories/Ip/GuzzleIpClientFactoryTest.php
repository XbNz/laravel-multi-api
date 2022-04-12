<?php

namespace XbNz\Resolver\Tests\Feature\Factories\Ip;

use GuzzleHttp\HandlerStack;
use Illuminate\Support\Facades\Config;
use XbNz\Resolver\Factories\Ip\GuzzleIpClientFactory;
use XbNz\Resolver\Support\Actions\UniversalMiddlewaresAction;

class GuzzleIpClientFactoryTest extends \XbNz\Resolver\Tests\TestCase
{
    /** @test **/
    public function the_universal_middlewares_apply_to_the_guzzle_client(): void
    {
        // Arrange
        Config::set('resolver.timeout', 30);
        Config::set('resolver.use_retries', true);
        Config::set('resolver.retry_sleep', 2);
        Config::set('resolver.retry_sleep_multiplier', 2);
        Config::set('resolver.async_concurrent_requests', 100);
        Config::set('resolver.use_proxy', true);
        Config::set('resolver.proxies', ['https://1.1.1.1:8080']);

        // Act
        $client = app(GuzzleIpClientFactory::class)
            ->for('::doesnt-matter-this-should-be-provider-agnostic::');

        // Assert
        $handler = invade(invade($client)->config['handler']);
        $this->assertIsInt($handler->findByName('timeout'));
        $this->assertIsInt($handler->findByName('proxy'));
        $this->assertIsInt($handler->findByName('caching'));
    }
}