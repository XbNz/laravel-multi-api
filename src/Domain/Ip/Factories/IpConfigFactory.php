<?php

namespace XbNz\Resolver\Domain\Ip\Factories;

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
use XbNz\Resolver\Domain\Ip\Strategies\UriStrategies\IpGeolocationDotIoStrategy;
use XbNz\Resolver\Support\Actions\GetRandomApiKeyAction;
use XbNz\Resolver\Support\Actions\UniversalMiddlewaresAction;
use XbNz\Resolver\Support\DTOs\GuzzleConfigData;
use XbNz\Resolver\Support\Exceptions\ConfigNotFoundException;
use XbNz\Resolver\Support\Guzzle\Middlewares\WithRetry;

class IpConfigFactory
{
    public function __construct(
        private UniversalMiddlewaresAction $universalMiddlewares,
        private VerifyIpIntegrityAction $verifyIpIntegrity,
        private GetRandomApiKeyAction $getRandomApiKey,
        private $soloIpStrategies,
        private $authStrategies,
        private $retryStrategies,
    )
    {}

    /**
     * @param string $ip Accepts Ipv4 or Ipv6
     * @param string $provider API host. e.g. ipgeolocation.io. Refer to config file for all supported hosts.
     * @throws \XbNz\Resolver\Support\Exceptions\MissingApiKeyException
     * @throws \XbNz\Resolver\Domain\Ip\Exceptions\InvalidIpAddressException
     * @throws ConfigNotFoundException
     */
    public function for(string $ip, string $provider, $overrides = []): GuzzleConfigData
    {
        $this->getRandomApiKey->execute($provider, 'ip-resolver.api-keys');

        $contextualMiddlewares = [];

        $contextualMiddlewares[] = Collection::make($this->soloIpStrategies)
            ->first(fn (SoloIpStrategy $strategy) => $strategy->supports($provider), new NullStrategy())
            ->guzzleMiddleware($this->verifyIpIntegrity->execute($ip));

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

        return new GuzzleConfigData(
            $data['middlewares']
        );
    }

}