<?php

declare(strict_types=1);

namespace XbNz\Resolver\Tests\Unit\Ip\Strategies;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Config;
use XbNz\Resolver\Domain\Ip\Drivers\AbuseIpDbDotComDriver;
use XbNz\Resolver\Domain\Ip\Drivers\IpApiDotComDriver;
use XbNz\Resolver\Domain\Ip\Drivers\IpDataDotCoDriver;
use XbNz\Resolver\Domain\Ip\Drivers\IpGeolocationDotIoDriver;
use XbNz\Resolver\Domain\Ip\Strategies\RetryStrategies\AbuseIpDbDotComStrategy;
use XbNz\Resolver\Domain\Ip\Strategies\RetryStrategies\IpApiDotComStrategy;
use XbNz\Resolver\Domain\Ip\Strategies\RetryStrategies\IpDataDotCoStrategy;
use XbNz\Resolver\Domain\Ip\Strategies\RetryStrategies\IpGeolocationDotIoStrategy;
use XbNz\Resolver\Domain\Ip\Strategies\RetryStrategies\MtrDotShMtrStrategy;

class RetryStrategiesTest extends \XbNz\Resolver\Tests\TestCase
{
    /** @test **/
    public function the_number_of_retries_in_the_config_equates_to_the_number_of_failed_response_meaning_it_retries_on_all_of_them_as_intended(): void
    {
        // Arrange
        Config::set([
            'resolver.use_retries' => true,
            'resolver.tries' => 10,
            'resolver.retry_sleep' => .0001,
        ]);

        $mockQueue = [
            new Response(400),
            new Response(401),
            new Response(403),
            new Response(408),
            new Response(429),
            new Response(500),
            new Response(502),
            new Response(503),
            new Response(504),
            new ConnectException('Test', new Request('GET', '/')),
            new Response(200),
        ];

        $testedStrategies = [
            AbuseIpDbDotComStrategy::class,
            IpDataDotCoStrategy::class,
            IpGeolocationDotIoStrategy::class,
        ];

        foreach ($testedStrategies as $strategy) {
            $mockHandler = new MockHandler($mockQueue);
            $stack = HandlerStack::create($mockHandler);
            $stack->push(app($strategy)->guzzleMiddleware());
            $client = new Client([
                'handler' => $stack,
            ]);
            $client->request('GET', '/');
        }

        // Assert
        $this->expectNotToPerformAssertions();
    }

    /** @test **/
    public function the_number_of_retries_in_the_config_falls_short_of_the_number_of_failed_response_meaning_a_guzzle_exception_is_thrown(): void
    {
        // Arrange
        Config::set([
            'resolver.use_retries' => true,
            'resolver.tries' => 1,
            'resolver.retry_sleep' => .0001,
        ]);

        $mockQueue = [
            new Response(400),
            new ConnectException('Test', new Request('GET', '/')),
            new Response(200),
        ];

        $testedStrategies = [
            AbuseIpDbDotComStrategy::class,
            IpDataDotCoStrategy::class,
            IpGeolocationDotIoStrategy::class,
            MtrDotShMtrStrategy::class,
        ];

        foreach ($testedStrategies as $strategy) {
            try {
                $mockHandler = new MockHandler($mockQueue);
                $stack = HandlerStack::create($mockHandler);
                $stack->push(app($strategy)->guzzleMiddleware());
                $client = new Client([
                    'handler' => $stack,
                ]);
                $client->request('GET', '/');
            } catch (TransferException $e) {
                $this->assertInstanceOf(TransferException::class, $e);
                return;
            }
        }

        // Assert
        $this->fail('No exception was thrown');
    }

    /** @test **/
    public function the_token_is_refreshed_for_abuse_ip_db_on_retry(): void
    {
        // Arrange
        $driver = AbuseIpDbDotComDriver::class;
        Config::set([
            'resolver.use_retries' => true,
            'resolver.tries' => 2,
            'resolver.retry_sleep' => .0001,
            "ip-resolver.api-keys.{$driver}" => 'should-be-this',
        ]);

        $mockQueue = [
            new Response(400),
            new ConnectException('Test', new Request('GET', '/')),
            new Response(200),
        ];

        $mockHandler = new MockHandler($mockQueue);
        $stack = HandlerStack::create($mockHandler);
        $stack->push(app(AbuseIpDbDotComStrategy::class)->guzzleMiddleware());
        $client = new Client([
            'handler' => $stack,
        ]);

        // Act

        $client->request('GET', '/', [
            'headers' => [
                'key' => 'should-not-be-this',
                
            ], ]);

        // Assert

        $this->assertSame('should-be-this', $mockHandler->getLastRequest()?->getHeader('key')[0] ?? 'not-found');
    }

    /** @test **/
    public function the_token_is_refreshed_for_ip_data_on_retry(): void
    {
        // Arrange
        $driver = IpDataDotCoDriver::class;
        Config::set([
            'resolver.use_retries' => true,
            'resolver.tries' => 2,
            'resolver.retry_sleep' => .0001,
            "ip-resolver.api-keys.{$driver}" => 'should-be-this',
        ]);

        $mockQueue = [
            new Response(400),
            new ConnectException('Test', new Request('GET', '/')),
            new Response(200),
        ];

        $mockHandler = new MockHandler($mockQueue);
        $stack = HandlerStack::create($mockHandler);
        $stack->push(app(IpDataDotCoStrategy::class)->guzzleMiddleware());
        $client = new Client([
            'handler' => $stack,
        ]);

        // Act

        $client->request('GET', '/', [
            'query' => [
                'Shall-not-be-removed' => 'test-key',
            ],
        ]);

        // Assert

        $this->assertStringContainsString(
            'should-be-this',
            $mockHandler->getLastRequest()->getUri()->getQuery()
        );

        $this->assertStringContainsString(
            'Shall-not-be-removed',
            $mockHandler->getLastRequest()->getUri()->getQuery()
        );
    }

    /** @test **/
    public function the_token_is_refreshed_for_ip_geolocation_on_retry(): void
    {
        // Arrange
        $driver = IpGeolocationDotIoDriver::class;
        Config::set([
            'resolver.use_retries' => true,
            'resolver.tries' => 2,
            'resolver.retry_sleep' => .0001,
            "ip-resolver.api-keys.{$driver}" => 'should-be-this',
        ]);

        $mockQueue = [
            new Response(400),
            new ConnectException('Test', new Request('GET', '/')),
            new Response(200),
        ];

        $mockHandler = new MockHandler($mockQueue);
        $stack = HandlerStack::create($mockHandler);
        $stack->push(app(IpGeolocationDotIoStrategy::class)->guzzleMiddleware());
        $client = new Client([
            'handler' => $stack,
        ]);

        // Act

        $client->request('GET', '/', [
            'query' => [
                'Shall-not-be-removed' => 'test-key',
            ],
        ]);

        // Assert

        $this->assertStringContainsString(
            'should-be-this',
            $mockHandler->getLastRequest()->getUri()->getQuery()
        );

        $this->assertStringContainsString(
            'Shall-not-be-removed',
            $mockHandler->getLastRequest()->getUri()->getQuery()
        );
    }

    /** @test **/
    public function the_token_is_refreshed_for_ip_api_on_retry(): void
    {
        // Arrange
        $driver = IpApiDotComDriver::class;
        Config::set([
            'resolver.use_retries' => true,
            'resolver.tries' => 2,
            'resolver.retry_sleep' => .0001,
            "ip-resolver.api-keys.{$driver}" => 'should-be-this',
        ]);

        $mockQueue = [
            new Response(400),
            new ConnectException('Test', new Request('GET', '/')),
            new Response(200),
        ];

        $mockHandler = new MockHandler($mockQueue);
        $stack = HandlerStack::create($mockHandler);
        $stack->push(app(IpApiDotComStrategy::class)->guzzleMiddleware());
        $client = new Client([
            'handler' => $stack,
        ]);

        // Act

        $client->request('GET', '/', [
            'query' => [
                'Shall-not-be-removed' => 'test-key',
            ],
        ]);

        // Assert

        $this->assertStringContainsString(
            'should-be-this',
            $mockHandler->getLastRequest()->getUri()->getQuery()
        );

        $this->assertStringContainsString(
            'Shall-not-be-removed',
            $mockHandler->getLastRequest()->getUri()->getQuery()
        );
    }
}
