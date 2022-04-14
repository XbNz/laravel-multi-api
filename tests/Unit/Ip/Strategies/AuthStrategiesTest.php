<?php

namespace XbNz\Resolver\Tests\Unit\Ip\Strategies;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Config;
use XbNz\Resolver\Domain\Ip\Drivers\AbuseIpDbDotComDriver;
use XbNz\Resolver\Domain\Ip\Drivers\IpDataDotCoDriver;
use XbNz\Resolver\Domain\Ip\Drivers\IpGeolocationDotIoDriver;
use XbNz\Resolver\Domain\Ip\Strategies\AuthStrategies\AbuseIpDbDotComStrategy;
use XbNz\Resolver\Domain\Ip\Strategies\AuthStrategies\IpDataDotCoStrategy;
use XbNz\Resolver\Domain\Ip\Strategies\AuthStrategies\IpGeolocationDotIoStrategy;
use function app;

class AuthStrategiesTest extends \XbNz\Resolver\Tests\TestCase
{
    /** @test **/
    public function it_retrieves_a_random_key_for_abuse_ip_db_and_applies_it_to_the_key_header_without_removing_previous_values(): void
    {
        $driverFQCN = AbuseIpDbDotComDriver::class;
        Config::set([
            "ip-resolver.api-keys.{$driverFQCN}" => 'this-should-be-the-key-below'
        ]);

        $mockHandler = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], '{"::success::": true}'),
        ]);

        $stack = HandlerStack::create($mockHandler);
        $stack->push(app(AbuseIpDbDotComStrategy::class)->guzzleMiddleware());
        $client = new Client(['handler' => $stack]);

        // Act

         $client->request('GET', '/', [
            'headers' => [
                'Shall-not-be-removed' => 'test-key',
            ],
        ]);


        // Assert

        $this->assertTrue($mockHandler->getLastRequest()->hasHeader('Shall-not-be-removed'));
        $this->assertSame('this-should-be-the-key-below', $mockHandler->getLastRequest()->getHeader('key')[0]);
    }

    /** @test **/
    public function it_retrieves_a_random_key_for_ip_data_and_applies_it_to_the_key_path_without_removing_previous_paths(): void
    {
        $driverFQCN = IpDataDotCoDriver::class;
        Config::set([
            "ip-resolver.api-keys.{$driverFQCN}" => 'this-should-be-the-key-below'
        ]);

        $mockHandler = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], '{"::success::": true}'),
        ]);

        $stack = HandlerStack::create($mockHandler);
        $stack->push(app(IpDataDotCoStrategy::class)->guzzleMiddleware());
        $client = new Client(['handler' => $stack]);

        // Act

        $client->request('GET', '/', [
            'query' => [
                'Shall-not-be-removed' => 'test-key',
            ],
        ]);


        // Assert


        $this->assertStringContainsString(
            'this-should-be-the-key-below',
            $mockHandler->getLastRequest()->getUri()->getQuery()
        );

        $this->assertStringContainsString(
            'Shall-not-be-removed',
            $mockHandler->getLastRequest()->getUri()->getQuery()
        );
    }

    /** @test **/
    public function it_retrieves_a_random_key_for_ip_geolocation_and_applies_it_to_the_key_path_without_removing_previous_paths(): void
    {
        $driverFQCN = IpGeolocationDotIoDriver::class;
        Config::set([
            "ip-resolver.api-keys.{$driverFQCN}" => 'this-should-be-the-key-below'
        ]);

        $mockHandler = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], '{"::success::": true}'),
        ]);

        $stack = HandlerStack::create($mockHandler);
        $stack->push(app(IpGeolocationDotIoStrategy::class)->guzzleMiddleware());
        $client = new Client(['handler' => $stack]);

        // Act

        $client->request('GET', '/', [
            'query' => [
                'Shall-not-be-removed' => 'test-key',
            ],
        ]);


        // Assert


        $this->assertStringContainsString(
            'this-should-be-the-key-below',
            $mockHandler->getLastRequest()->getUri()->getQuery()
        );

        $this->assertStringContainsString(
            'Shall-not-be-removed',
            $mockHandler->getLastRequest()->getUri()->getQuery()
        );
    }

}