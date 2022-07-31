<?php

declare(strict_types=1);

namespace XbNz\Resolver\Factories;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Webmozart\Assert\Assert;
use XbNz\Resolver\Domain\Ip\Services\Service;
use XbNz\Resolver\Support\Strategies\AuthStrategy;
use XbNz\Resolver\Support\Strategies\NullStrategy;
use XbNz\Resolver\Support\Strategies\ResponseFormatterStrategy;
use XbNz\Resolver\Support\Strategies\RetryStrategy;

class GuzzleClientFactory
{
    /**
     * @param array<AuthStrategy> $authStrategies
     * @param array<RetryStrategy> $retryStrategies
     * @param array<ResponseFormatterStrategy> $responseFormatters
     */
    public function __construct(
        private readonly UniversalMiddlewaresFactory $universalMiddlewares,
        private readonly array $authStrategies,
        private readonly array $retryStrategies,
        private readonly array $responseFormatters
    ) {
    }

    /**
     * @param class-string<Service> $service Service FQN e.g. MtrDotToolsService::class
     * @param array<array<callable>> $overrides
     */
    public function for(string $service, array $overrides = []): Client
    {
        $contextualMiddlewares = [];

        $contextualMiddlewares['auth_strategy'] = Collection::make($this->authStrategies)
            ->first(fn (AuthStrategy $strategy) => $strategy->supports($service), new NullStrategy())
            ->guzzleMiddleware();

        $contextualMiddlewares['response_formatter'] = Collection::make($this->responseFormatters)
            ->first(fn (ResponseFormatterStrategy $strategy) => $strategy->supports($service), new NullStrategy())
            ->guzzleMiddleware();

        if ((bool) Config::get('resolver.use_retries', false)) {
            $contextualMiddlewares['retry_strategy'] = Collection::make($this->retryStrategies)
                ->first(fn (RetryStrategy $strategy) => $strategy->supports($service), new NullStrategy())
                ->guzzleMiddleware();
        }

        $data = array_merge([
            'middlewares' => [
                ...$this->universalMiddlewares->guzzleMiddlewares(),
                ...array_filter($contextualMiddlewares),
            ],
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
