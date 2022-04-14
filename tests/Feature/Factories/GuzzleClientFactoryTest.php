<?php

namespace XbNz\Resolver\Tests\Feature\Factories;

use Illuminate\Support\Facades\Config;
use XbNz\Resolver\Domain\Ip\Builders\IpBuilder;
use XbNz\Resolver\Domain\Ip\Drivers\IpGeolocationDotIoDriver;
use XbNz\Resolver\Factories\GuzzleClientFactory;
use XbNz\Resolver\Tests\Feature\Fakes\FakeDriver;
use XbNz\Resolver\Tests\Feature\Fakes\FakeGuzzleAuthStrategy;
use XbNz\Resolver\Tests\Feature\Fakes\FakeGuzzleFormatterStrategy;
use XbNz\Resolver\Tests\Feature\Fakes\FakeGuzzleRetryStrategy;
use function app;
use function invade;

class GuzzleClientFactoryTest extends \XbNz\Resolver\Tests\TestCase
{

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('resolver.timeout', 30);
        Config::set('resolver.use_retries', true);
        Config::set('resolver.retry_sleep', 2);
        Config::set('resolver.retry_sleep_multiplier', 2);
        Config::set('resolver.async_concurrent_requests', 100);
        Config::set('resolver.use_proxy', true);
        Config::set('resolver.proxies', ['https://1.1.1.1:8080']);


        $this->app->tag([FakeGuzzleAuthStrategy::class], 'auth-strategies');
        $this->app->tag([FakeGuzzleRetryStrategy::class], 'retry-strategies');
        $this->app->tag([FakeGuzzleFormatterStrategy::class], 'response-formatters');
    }

    /** @test **/
    public function the_universal_middlewares_apply_to_the_guzzle_client(): void
    {
        // Arrange


        // Act
        $client = app(GuzzleClientFactory::class)
            ->for('::doesnt-matter-this-should-be-provider-agnostic::');


        // Assert
        $handler = invade(invade($client)->config['handler']);

        $this->assertIsInt($handler->findByName('timeout'));
        $this->assertIsInt($handler->findByName('proxy'));
        $this->assertIsInt($handler->findByName('caching'));
    }

    /** @test **/
    public function contextual_middlewares_are_applied_on_a_driver_by_driver_basis_depending_on_what_is_available(): void
    {
        // Arrange
        $client = app(GuzzleClientFactory::class)
            ->for(FakeDriver::class);
        $clientB = app(GuzzleClientFactory::class)
            ->for('::something-that-does-not-exist::');

        // Act
        $handler = invade(invade($client)->config['handler']);
        $handlerB = invade(invade($clientB)->config['handler']);


        // Assert
        $this->assertIsInt($handler->findByName('auth_strategy'));
        $this->assertIsInt($handler->findByName('response_formatter'));
        $this->assertIsInt($handler->findByName('retry_strategy'));

        try {
            $this->assertNull($handlerB->findByName('auth_strategy'));
        } catch (\InvalidArgumentException $e) {
            $this->assertStringContainsString('auth_strategy', $e->getMessage());
        }

        try {
            $this->assertNull($handlerB->findByName('retry_strategy'));
        } catch (\InvalidArgumentException $e) {
            $this->assertStringContainsString('retry_strategy', $e->getMessage());
        }

        try {
            $this->assertNull($handlerB->findByName('response_formatter'));
        } catch (\InvalidArgumentException $e) {
            $this->assertStringContainsString('response_formatter', $e->getMessage());
            return;
        }

        $this->fail('Should have thrown an exception');
    }

    /** @test **/
    public function retry_middlewares_are_exempted_from_the_stack_if_the_retry_config_is_off(): void
    {
        // Arrange
        Config::set('resolver.use_retries', false);

        $client = app(GuzzleClientFactory::class)
            ->for(FakeDriver::class);

        // Act
        $handler = invade(invade($client)->config['handler']);


        // Assert
        $this->assertIsInt($handler->findByName('auth_strategy'));
        $this->assertIsInt($handler->findByName('response_formatter'));

        try {
            $this->assertNull($handler->findByName('retry_strategy'));
        } catch (\InvalidArgumentException $e) {
            $this->assertStringContainsString('retry_strategy', $e->getMessage());
            return;
        }

        $this->fail('Should have thrown an exception');
    }
}