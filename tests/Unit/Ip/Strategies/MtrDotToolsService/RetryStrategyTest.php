<?php

declare(strict_types=1);

namespace XbNz\Resolver\Tests\Unit\Ip\Strategies\MtrDotToolsService;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Config;
use XbNz\Resolver\Domain\Ip\Strategies\RetryStrategies\MtrDotToolsStrategy;
use XbNz\Resolver\Tests\TestCase;

class RetryStrategyTest extends TestCase
{
    /** @test **/
    public function the_number_of_retries_in_the_config_equates_to_the_number_of_failed_responses_meaning_it_retries_on_all_of_them_as_intended(): void
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

        $mockHandler = new MockHandler($mockQueue);
        $stack = HandlerStack::create($mockHandler);
        $stack->push(app(MtrDotToolsStrategy::class)->guzzleMiddleware());
        $client = new Client([
            'handler' => $stack,
        ]);

        $client->request('GET', '/');

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

        try {
            $mockHandler = new MockHandler($mockQueue);
            $stack = HandlerStack::create($mockHandler);
            $stack->push(app(MtrDotToolsStrategy::class)->guzzleMiddleware());
            $client = new Client([
                'handler' => $stack,
            ]);
            $client->request('GET', '/');
        } catch (TransferException $e) {
            $this->assertInstanceOf(TransferException::class, $e);
            return;
        }

        // Assert
        $this->fail('No exception was thrown');
    }
}
