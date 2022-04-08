<?php

namespace XbNz\Resolver\Support\Factories;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleRetry\GuzzleRetryMiddleware;
use Illuminate\Support\Facades\Config;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Support\Actions\UniversalMiddlewaresAction;
use XbNz\Resolver\Support\DTOs\GuzzleConfigData;
use XbNz\Resolver\Support\Guzzle\Middlewares\WithRetry;

class GuzzleConfigFactory
{
    public function __construct(
        private UniversalMiddlewaresAction $universalMiddlewares
    )
    {}

    public function forIpGeolocationDotIo(IpData $ip, $overrides = []): GuzzleConfigData
    {

        $data = array_merge([
            'base_uri' => 'https://api.ipgeolocation.io/',
            'request' => new Request('GET', '/ipgeo/'),
            'query_params' => [
                'apiKey' => '4ccb1d5f495b461aa6348dd168b77d03', // TODO: Implement new random key retrieval method
                'ip' => $ip->ip,
            ],
            // TODO: Remove query params from dto altogether and implement an auth, ip, and uri strategy per provider
            'middlewares' => [
                ...$this->universalMiddlewares->execute(),
                $this->withRetry(
                    static function (
                        int $attemptNumber,
                        float $delay,
                        RequestInterface &$request,
                        array &$options,
                        ?ResponseInterface $response
                    ) {
                        dump('This is the retry closure running!');
                    }
                )
            ]
        ], $overrides);

        return new GuzzleConfigData(
            $data['base_uri'],
            $data['request'],
            $data['query_params'],
            $data['middlewares']
        );
    }

    private function withRetry(callable $fetchTokenLogic): callable
    {
        if (Config::get('resolver.use_retries')) {
            return (new WithRetry)(
                Config::get('resolver.tries', 5),
                Config::get('resolver.retry_sleep', 2),
                Config::get('retry_sleep_multiplier', 1.5),
                runOnRetryCallable: $fetchTokenLogic
            );
        }
    }
}