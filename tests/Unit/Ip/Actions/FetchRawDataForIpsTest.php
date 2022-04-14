<?php

namespace XbNz\Resolver\Tests\Unit\Ip\Actions;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Collection;
use XbNz\Resolver\Domain\Ip\Actions\FetchRawDataForIpsAction;
use XbNz\Resolver\Domain\Ip\Builders\DriverBuilder;
use XbNz\Resolver\Domain\Ip\Drivers\AbuseIpDbDotComDriver;
use XbNz\Resolver\Domain\Ip\Drivers\IpGeolocationDotIoDriver;
use XbNz\Resolver\Domain\Ip\Drivers\MtrDotShMtrDriver;
use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Domain\Ip\DTOs\RawIpResultsData;
use XbNz\Resolver\Domain\Ip\Mappings\MtrDotShMtrMapper;
use XbNz\Resolver\Factories\Ip\GuzzleIpClientFactory;
use XbNz\Resolver\Factories\Ip\IpDataFactory;
use XbNz\Resolver\Factories\Ip\MappedResultFactory;
use XbNz\Resolver\Support\Drivers\Driver;
use XbNz\Resolver\Support\Exceptions\ApiProviderException;
use XbNz\Resolver\Tests\Feature\Fakes\FakeDriver;
use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\containsOnlyInstancesOf;

class FetchRawDataForIpsTest extends \XbNz\Resolver\Tests\TestCase
{
    /** @test **/
    public function given_a_set_of_ips_and_drivers_if_a_successful_response_is_received_it_returns_instance_of_raw_ip_Data(): void
    {
        // Arrange
        $driverMock = $this->mock(Driver::class);
        $driverMock = $driverMock
            ->shouldReceive('getRequests')
            ->once()
            ->withAnyArgs() // TODO: with() and only ins of IpData
            ->andreturn(Collection::make([
                new Request('GET', '/'),
            ]));

        $mockHandler = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], '{"success": true}'),
        ]);

        $client = new Client(['handler' => HandlerStack::create($mockHandler)]);


        $factoryMock = $this->mock(GuzzleIpClientFactory::class);
        $factoryMock->shouldReceive('for')
            ->once()
            ->with($driverMock->getMock()::class)
            ->andReturn($client);

        // Act

        $action =  app(FetchRawDataForIpsAction::class);

        $rawResponses = $action->execute([IpDataFactory::fromIp('1.1.1.1')], [$driverMock->getMock()]);

        // Assert

        $this->assertSame(true, $rawResponses[0]->data['success']);
        // TODO: Instance of assertion for ->provider

    }

    /** @test **/
    public function given_a_set_of_ips_and_drivers_if_a_failed_response_is_received_it_throws_an_api_exception(): void
    {
        // Arrange
        $driverMock = $this->mock(Driver::class);
        $driverMock = $driverMock
            ->shouldReceive('getRequests')
            ->once()
            ->withAnyArgs() // TODO: with() and only ins of IpData
            ->andreturn(Collection::make([
                new Request('GET', '/'),
                new Request('GET', '/'),
                new Request('GET', '/'),
                new Request('GET', '/'),
                new Request('GET', '/'),
            ]));

        $mockHandler = new MockHandler([
            // TODO: Make it work with more than 1 response
            new Response(401),
            new Response(403),
            new Response(408),
            new Response(429),
            new ConnectException('Test', new Request('GET', '/')),
        ]);

        $client = new Client(['handler' => HandlerStack::create($mockHandler)]);


        $factoryMock = $this->mock(GuzzleIpClientFactory::class);
        $factoryMock->shouldReceive('for')
            ->once()
            ->with($driverMock->getMock()::class)
            ->andReturn($client);

        // Act

        $action = app(FetchRawDataForIpsAction::class);


        // Assert

//        $this->expectException(ApiProviderException::class);
        $action->execute([IpDataFactory::fromIp('1.1.1.1')], [$driverMock->getMock()]);

    }
}