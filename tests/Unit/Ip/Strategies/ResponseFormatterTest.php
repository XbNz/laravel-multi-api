<?php

declare(strict_types=1);

namespace XbNz\Resolver\Tests\Unit\Ip\Strategies;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use XbNz\Resolver\Domain\Ip\Strategies\ResponseFormatterStratagies\IpApiDotComStrategy;
use XbNz\Resolver\Domain\Ip\Strategies\ResponseFormatterStratagies\MtrDotShMtrStrategy;
use XbNz\Resolver\Factories\Ip\RekindledMtrDotShFactory;
use XbNz\Resolver\Tests\TestCase;

class ResponseFormatterTest extends TestCase
{
    /** @test **/
    public function a_plain_text_mtr_response_is_converted_to_json(): void
    {
        // Arrange
        $plainText = RekindledMtrDotShFactory::generateTestData()->plainTextBody;

        $mockHandler = new MockHandler([
            new Response(200, [
                'Content-Type' => 'text/plain',
            ], $plainText),
        ]);

        $stack = HandlerStack::create($mockHandler);
        $stack->push(app(MtrDotShMtrStrategy::class)->guzzleMiddleware());
        $client = new Client([
            'handler' => $stack,
        ]);

        // Act
        $response = $client->request('GET', '/ss2d3/mtr/1.1.1.1');

        // Assert

        $this->assertSame('application/json', $response->getHeader('Content-Type')[0]);

        json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);
    }

    /** @test **/
    public function ip_api_throws_a_200_for_failed_authentication_lets_fix_that_for_them_haha(): void
    {
        // Arrange

        $mockHandler = new MockHandler([
            new Response(200, [
                'Content-Type' => 'application/json',
            ], '{ "success": false }'),
        ]);

        $stack = HandlerStack::create($mockHandler);
        $stack->push(app(IpApiDotComStrategy::class)->guzzleMiddleware());
        $client = new Client([
            'handler' => $stack,
        ]);

        // Act

        try {
            $response = $client->request('GET', '/');
        } catch (ClientException $e) {
            $this->assertSame(401, $e->getCode());
            return;
        }

        // Assert

        $this->fail('Expected exception');
    }
}
