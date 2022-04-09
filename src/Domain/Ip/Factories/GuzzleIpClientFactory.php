<?php

namespace XbNz\Resolver\Domain\Ip\Factories;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Uri;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ItemNotFoundException;
use Illuminate\Support\Str;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use XbNz\Resolver\Domain\Ip\Actions\VerifyIpIntegrityAction;
use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Domain\Ip\Strategies\AuthStrategies\AuthStrategy;
use XbNz\Resolver\Domain\Ip\Strategies\NullStrategy;
use XbNz\Resolver\Domain\Ip\Strategies\RetryStrategies\RetryStrategy;
use XbNz\Resolver\Domain\Ip\Strategies\SoloIpAddressStrategies\SoloIpStrategy;
use XbNz\Resolver\Support\Actions\GetRandomApiKeyAction;
use XbNz\Resolver\Support\Actions\UniversalMiddlewaresAction;
use XbNz\Resolver\Support\DTOs\GuzzleConfigData;
use XbNz\Resolver\Support\Exceptions\ConfigNotFoundException;
use XbNz\Resolver\Support\Guzzle\Middlewares\WithRetry;

class GuzzleIpClientFactory
{
    /**
     * @param array<SoloIpStrategy> $soloIpStrategies
     * @param array<AuthStrategy> $authStrategies
     * @param array<RetryStrategy> $retryStrategies
     */
    public function __construct(
        private UniversalMiddlewaresAction $universalMiddlewares,
        private array $authStrategies,
        private array $retryStrategies,
    )
    {}

    /**
     * @param string $provider API host. e.g. ipgeolocation.io. Refer to config file for all supported hosts.
     * @throws \XbNz\Resolver\Support\Exceptions\MissingApiKeyException
     * @throws \XbNz\Resolver\Domain\Ip\Exceptions\InvalidIpAddressException
     * @throws ConfigNotFoundException
     */
    public function for(string $provider, $overrides = []): Client
    {
        $contextualMiddlewares = [];

        $contextualMiddlewares[] = Collection::make($this->authStrategies)
            ->first(fn (AuthStrategy $strategy) => $strategy->supports($provider), new NullStrategy())
            ->guzzleMiddleware();

        if ((bool) Config::get('resolver.use_retries', false))
        {
            $contextualMiddlewares[] = Collection::make($this->retryStrategies)
                ->first(fn (RetryStrategy $strategy) => $strategy->supports($provider), new NullStrategy())
                ->guzzleMiddleware();
        }

        $data = array_merge([
            'middlewares' => [
                ...$this->universalMiddlewares->execute(),
                ...$contextualMiddlewares,
            ]
        ], $overrides);

        $dto = new GuzzleConfigData(
            $data['middlewares']
        );

        $stack = HandlerStack::create();

        foreach ($dto->middlewares as $middleware) {
            $stack->push($middleware);
        }

        return new Client([
            'handler' => $stack,
        ]);
    }

}