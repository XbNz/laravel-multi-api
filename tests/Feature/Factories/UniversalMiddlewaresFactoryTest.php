<?php

declare(strict_types=1);

namespace XbNz\Resolver\Tests\Feature\Factories;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use XbNz\Resolver\Factories\UniversalMiddlewaresFactory;

class UniversalMiddlewaresFactoryTest extends \XbNz\Resolver\Tests\TestCase
{
    /** @test **/
    public function the_content_is_cached_for_the_amount_specified_in_the_config(): void
    {
        // Arrange
        Config::set(['resolver.cache_period' => 3600]);

        $mockHandler = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], '{"::success::": true}'),
        ]);


        $stack = HandlerStack::create($mockHandler);
        $middlewares = app(UniversalMiddlewaresFactory::class)->guzzleMiddlewares();

        foreach ($middlewares as $middleware) {
            $stack->push($middleware);
        }

        $client = new Client(['handler' => $stack]);

        // Act

        $client->request('GET', '/');

        $cachedHttpResponseKey = array_keys(invade(Cache::getStore())->storage)[0];

        // Assert

        $this->assertStringContainsString(
            '::success::',
            Cache::get($cachedHttpResponseKey)
        );

        $shouldBe = Carbon::now()->addSeconds(3600);

        $diff = Carbon::parse(
            invade(Cache::getStore())->storage[$cachedHttpResponseKey]['expiresAt']
        )->diffInSeconds($shouldBe);

        $this->assertLessThan(1, $diff);
    }

    /** @test **/
    public function timeout_is_set_to_config_timeout(): void
    {
        // Arrange
        Config::set(['resolver.timeout' => 5]);

        $mockHandler = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], '{"::success::": true}'),
        ]);


        $stack = HandlerStack::create($mockHandler);
        $middlewares = app(UniversalMiddlewaresFactory::class)->guzzleMiddlewares();

        foreach ($middlewares as $name => $middleware) {
            $stack->push($middleware, $name);
        }

        $client = new Client(['handler' => $stack]);

        $handler = invade(invade($client)->config['handler']);

        $timeoutPositionInStack = $handler->findByName('timeout');

        $callable = $handler->stack[$timeoutPositionInStack][0];

        $reflection = new \ReflectionFunction($callable);
        $variables = $reflection->getStaticVariables();

        // Act


        // Assert

        $this->assertSame(5.0, $variables['timeout']);
    }


    /** @test **/
    public function proxy_is_set_to_config_proxies(): void
    {
        // Arrange
        $proxies = [
            'https://proxy.example.com',
            'https://wow.hello.org',
            'socks5://socks.example.com',
        ];

        Config::set([
            'resolver.use_proxy' => true,
            'resolver.proxies' => $proxies
        ]);

        $mockHandler = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], '{"::success::": true}'),
        ]);

        $stack = HandlerStack::create($mockHandler);
        $middlewares = app(UniversalMiddlewaresFactory::class)->guzzleMiddlewares();

        foreach ($middlewares as $name => $middleware) {
            $stack->push($middleware, $name);
        }

        $client = new Client(['handler' => $stack]);

        $handler = invade(invade($client)->config['handler']);

        $proxiesPositionInStack = $handler->findByName('proxy');

        $callable = $handler->stack[$proxiesPositionInStack][0];


        $reflection = new \ReflectionFunction($callable);
        $variables = $reflection->getStaticVariables();

        // Act


        // Assert
        $this->assertcontains($variables['proxy'], $proxies);
    }
}