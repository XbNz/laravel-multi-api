<?php

namespace XbNz\Resolver\Tests\Unit\Support\Actions;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Collection;
use XbNz\Resolver\Factories\GuzzleClientFactory;
use XbNz\Resolver\Factories\Ip\IpDataFactory;
use XbNz\Resolver\Support\Actions\FetchRawDataAction;
use XbNz\Resolver\Support\Drivers\Driver;
use XbNz\Resolver\Support\Exceptions\ApiProviderException;
use function app;

class FetchRawDataTest extends \XbNz\Resolver\Tests\TestCase
{
    /** @test **/
    public function given_a_set_of_ips_and_drivers_if_a_successful_response_is_received_it_returns_instance_of_raw_ip_Data(): void
    {
        // Arrange
        $driverMock = $this->mock(Driver::class);
        $driverMock = $driverMock
            ->shouldReceive('getRequests')
            ->once()
            ->withAnyArgs()
            ->andreturn(Collection::make([
                new Request('GET', '/'),
            ]));

        $mockHandler = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], '{"::success::": true}'),
        ]);

        $client = new Client(['handler' => HandlerStack::create($mockHandler)]);


        $factoryMock = $this->mock(GuzzleClientFactory::class);
        $factoryMock->shouldReceive('for')
            ->once()
            ->with($driverMock->getMock()::class)
            ->andReturn($client);

        // Act

        $action =  app(FetchRawDataAction::class);

        $rawResponses = $action->execute([IpDataFactory::fromIp('1.1.1.1')], [$driverMock->getMock()]);

        // Assert

        $this->assertSame(true, $rawResponses[0]->data['::success::']);

    }

    /** @test **/
    public function api_exception_401(): void
    {
        // Arrange
        $driverMock = $this->mock(Driver::class);
        $driverMock = $driverMock
            ->shouldReceive('getRequests')
            ->once()
            ->withAnyArgs()
            ->andreturn(Collection::make([
                new Request('GET', '/'),
            ]));

        $mockHandler = new MockHandler([
            new Response(401),
        ]);

        $client = new Client(['handler' => HandlerStack::create($mockHandler)]);

        $factoryMock = $this->mock(GuzzleClientFactory::class);
        $factoryMock->shouldReceive('for')
            ->once()
            ->with($driverMock->getMock()::class)
            ->andReturn($client);

        // Act
        $action = app(FetchRawDataAction::class);


        // Assert
        try {
            $action->execute([IpDataFactory::fromIp('1.1.1.1')], [$driverMock->getMock()]);
        } catch (ApiProviderException $e) {
            $this->assertStringContainsString('401', $e->getMessage());
            return;
        }

        $this->fail('Expected ApiProviderException to be thrown');
    }

    /** @test **/
    public function api_exception_403(): void
    {
        // Arrange
        $driverMock = $this->mock(Driver::class);
        $driverMock = $driverMock
            ->shouldReceive('getRequests')
            ->once()
            ->withAnyArgs()
            ->andreturn(Collection::make([
                new Request('GET', '/'),
            ]));

        $mockHandler = new MockHandler([
            new Response(403),
        ]);

        $client = new Client(['handler' => HandlerStack::create($mockHandler)]);

        $factoryMock = $this->mock(GuzzleClientFactory::class);
        $factoryMock->shouldReceive('for')
            ->once()
            ->with($driverMock->getMock()::class)
            ->andReturn($client);

        // Act
        $action = app(FetchRawDataAction::class);

        // Assert
        try {
            $action->execute([IpDataFactory::fromIp('1.1.1.1')], [$driverMock->getMock()]);
        } catch (ApiProviderException $e) {
            $this->assertStringContainsString('403', $e->getMessage());
            return;
        }

        $this->fail('Expected ApiProviderException to be thrown');
    }

    /** @test **/
    public function api_exception_408(): void
    {
        // Arrange
        $driverMock = $this->mock(Driver::class);
        $driverMock = $driverMock
            ->shouldReceive('getRequests')
            ->once()
            ->withAnyArgs()
            ->andreturn(Collection::make([
                new Request('GET', '/'),
            ]));

        $mockHandler = new MockHandler([
            new Response(408),
        ]);

        $client = new Client(['handler' => HandlerStack::create($mockHandler)]);

        $factoryMock = $this->mock(GuzzleClientFactory::class);
        $factoryMock->shouldReceive('for')
            ->once()
            ->with($driverMock->getMock()::class)
            ->andReturn($client);

        // Act
        $action = app(FetchRawDataAction::class);


        // Assert
        try {
            $action->execute([IpDataFactory::fromIp('1.1.1.1')], [$driverMock->getMock()]);
        } catch (ApiProviderException $e) {
            $this->assertStringContainsString('408', $e->getMessage());
            return;
        }

        $this->fail('Expected ApiProviderException to be thrown');
    }

    /** @test **/
    public function api_exception_429(): void
    {
        // Arrange
        $driverMock = $this->mock(Driver::class);
        $driverMock = $driverMock
            ->shouldReceive('getRequests')
            ->once()
            ->withAnyArgs()
            ->andreturn(Collection::make([
                new Request('GET', '/'),
            ]));

        $mockHandler = new MockHandler([
            new Response(429),
        ]);

        $client = new Client(['handler' => HandlerStack::create($mockHandler)]);

        $factoryMock = $this->mock(GuzzleClientFactory::class);
        $factoryMock->shouldReceive('for')
            ->once()
            ->with($driverMock->getMock()::class)
            ->andReturn($client);

        // Act
        $action = app(FetchRawDataAction::class);


        // Assert
        try {
            $action->execute([IpDataFactory::fromIp('1.1.1.1')], [$driverMock->getMock()]);
        } catch (ApiProviderException $e) {
            $this->assertStringContainsString('429', $e->getMessage());
            return;
        }

        $this->fail('Expected ApiProviderException to be thrown');
    }

    /** @test **/
    public function api_exception_default_bad_response(): void
    {
        // Arrange
        $driverMock = $this->mock(Driver::class);
        $driverMock = $driverMock
            ->shouldReceive('getRequests')
            ->once()
            ->withAnyArgs()
            ->andreturn(Collection::make([
                new Request('GET', '/'),
            ]));

        $error = 404;
        $mockHandler = new MockHandler([
            new Response($error),
        ]);

        $client = new Client(['handler' => HandlerStack::create($mockHandler)]);

        $factoryMock = $this->mock(GuzzleClientFactory::class);
        $factoryMock->shouldReceive('for')
            ->once()
            ->with($driverMock->getMock()::class)
            ->andReturn($client);

        // Act
        $action = app(FetchRawDataAction::class);


        // Assert
        try {
            $action->execute([IpDataFactory::fromIp('1.1.1.1')], [$driverMock->getMock()]);
        } catch (ApiProviderException $e) {
            $this->assertStringContainsString($error, $e->getMessage());
            return;
        }

        $this->fail('Expected ApiProviderException to be thrown');
    }

    /** @test **/
    public function api_exception_anything_other_than_bad_response(): void
    {
        // Arrange
        $driverMock = $this->mock(Driver::class);
        $driverMock = $driverMock
            ->shouldReceive('getRequests')
            ->once()
            ->withAnyArgs()
            ->andreturn(Collection::make([
                new Request('GET', '/'),
            ]));

        $error = 404;
        $mockHandler = new MockHandler([
            new ConnectException('Test', new Request('GET', '/')),
        ]);

        $client = new Client(['handler' => HandlerStack::create($mockHandler)]);

        $factoryMock = $this->mock(GuzzleClientFactory::class);
        $factoryMock->shouldReceive('for')
            ->once()
            ->with($driverMock->getMock()::class)
            ->andReturn($client);

        // Act
        $action = app(FetchRawDataAction::class);


        // Assert
        try {
            $action->execute([IpDataFactory::fromIp('1.1.1.1')], [$driverMock->getMock()]);
        } catch (ApiProviderException $e) {
            $this->assertNotInstanceOf(BadResponseException::class, $e->getPrevious());
            return;
        }

        $this->fail('Expected ApiProviderException to be thrown');
    }
}