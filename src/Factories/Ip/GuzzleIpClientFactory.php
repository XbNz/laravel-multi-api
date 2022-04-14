<?php

namespace XbNz\Resolver\Factories\Ip;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Webmozart\Assert\Assert;
use XbNz\Resolver\Domain\Ip\Strategies\AuthStrategies\AuthStrategy;
use XbNz\Resolver\Domain\Ip\Strategies\NullStrategy;
use XbNz\Resolver\Domain\Ip\Strategies\ResponseFormatterStratagies\ResponseFormatterStrategy;
use XbNz\Resolver\Domain\Ip\Strategies\RetryStrategies\RetryStrategy;
use XbNz\Resolver\Domain\Ip\Strategies\SoloIpAddressStrategies\SoloIpStrategy;
use XbNz\Resolver\Support\Actions\UniversalMiddlewaresAction;
use XbNz\Resolver\Support\DTOs\GuzzleConfigData;
use XbNz\Resolver\Support\Exceptions\ConfigNotFoundException;

class GuzzleIpClientFactory
{
    /**
     * @param array<AuthStrategy> $authStrategies
     * @param array<RetryStrategy> $retryStrategies
     * @param array<ResponseFormatterStrategy> $responseFormatters
     */
    public function __construct(
        private UniversalMiddlewaresAction $universalMiddlewares,
        private array $authStrategies,
        private array $retryStrategies,
        private array $responseFormatters
    )
    {}

    /**
     * @param string $driver Driver FQN e.g. IpGeolocationDotIoDriver::class. Refer to config file for all supported drivers.
     * @throws \XbNz\Resolver\Support\Exceptions\MissingApiKeyException
     * @throws \XbNz\Resolver\Domain\Ip\Exceptions\InvalidIpAddressException
     * @throws ConfigNotFoundException
     */
    public function for(string $driver, $overrides = []): Client
    {
        $contextualMiddlewares = Collection::make();

        $contextualMiddlewares['auth_strategy'] = Collection::make($this->authStrategies)
            ->first(fn (AuthStrategy $strategy) => $strategy->supports($driver), new NullStrategy())
            ->guzzleMiddleware();

        $contextualMiddlewares['response_formatter'] = Collection::make($this->responseFormatters)
            ->first(fn (ResponseFormatterStrategy $strategy) => $strategy->supports($driver), new NullStrategy())
            ->guzzleMiddleware();

        if ((bool) Config::get('resolver.use_retries', false))
        {
            $contextualMiddlewares['retry_strategy'] = Collection::make($this->retryStrategies)
                ->first(fn (RetryStrategy $strategy) => $strategy->supports($driver), new NullStrategy())
                ->guzzleMiddleware();
        }


        $data = array_merge([
            'middlewares' => [
                ...$this->universalMiddlewares->execute(),
                ...$contextualMiddlewares->filter(),
            ]
        ], $overrides);

        $stack = HandlerStack::create();

        Assert::allIsCallable($data['middlewares']);

        foreach ($data['middlewares'] as $middlewareName => $middleware) {
            $stack->push($middleware, $middlewareName);
        }

        return new Client([
            'handler' => $stack,
        ]);
    }

}