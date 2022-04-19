<?php

declare(strict_types=1);

namespace XbNz\Resolver\ServiceProviders;

use XbNz\Resolver\Domain\Ip\Mappings\AbstractApiDotComMapper;
use XbNz\Resolver\Domain\Ip\Mappings\AbuseIpDbDotComMapper;
use XbNz\Resolver\Domain\Ip\Mappings\IpApiDotCoMapper;
use XbNz\Resolver\Domain\Ip\Mappings\IpApiDotComMapper;
use XbNz\Resolver\Domain\Ip\Mappings\IpDashApiDotComMapper;
use XbNz\Resolver\Domain\Ip\Mappings\IpDataDotCoMapper;
use XbNz\Resolver\Domain\Ip\Mappings\IpGeolocationDotIoMapper;
use XbNz\Resolver\Domain\Ip\Mappings\IpInfoDotIoMapper;
use XbNz\Resolver\Domain\Ip\Mappings\MtrDotShMtrMapper;


use XbNz\Resolver\Domain\Ip\Mappings\MtrDotShPingMapper;
use XbNz\Resolver\Domain\Ip\Strategies\AuthStrategies\AbstractApiDotComStrategy as AbstractApiDotComAuthStrategy;
use XbNz\Resolver\Domain\Ip\Strategies\AuthStrategies\AbuseIpDbDotComStrategy as AbuseIpDbDotComAuthStrategy;
use XbNz\Resolver\Domain\Ip\Strategies\AuthStrategies\IpApiDotComStrategy as IpApiDotComAuthStrategy;
use XbNz\Resolver\Domain\Ip\Strategies\AuthStrategies\IpDataDotCoStrategy as IpDataDotCoAuthStrategy;
use XbNz\Resolver\Domain\Ip\Strategies\AuthStrategies\IpGeolocationDotIoStrategy as IpGeolocationDotIoAuthStrategy;
use XbNz\Resolver\Domain\Ip\Strategies\AuthStrategies\IpInfoDotIoStrategy as IpInfoDotIoAuthStrategy;

use XbNz\Resolver\Domain\Ip\Strategies\ResponseFormatterStratagies\IpApiDotComStrategy as IpApiDotComFormatterStrategy;
use XbNz\Resolver\Domain\Ip\Strategies\ResponseFormatterStratagies\MtrDotShMtrStrategy as MtrDotShMtrFormatterStrategy;
use XbNz\Resolver\Domain\Ip\Strategies\ResponseFormatterStratagies\MtrDotShPingStrategy as MtrDotShPingFormatterStrategy;


use XbNz\Resolver\Domain\Ip\Strategies\RetryStrategies\AbstractApiDotComStrategy as AbstractApiDotComRetryStrategy;
use XbNz\Resolver\Domain\Ip\Strategies\RetryStrategies\AbuseIpDbDotComStrategy as AbuseIpDbDotComRetryStrategy;
use XbNz\Resolver\Domain\Ip\Strategies\RetryStrategies\IpApiDotComStrategy as IpApiDotComRetryStrategy;
use XbNz\Resolver\Domain\Ip\Strategies\RetryStrategies\IpApiDotCoStrategy as IpApiDotCoRetryStrategy;
use XbNz\Resolver\Domain\Ip\Strategies\RetryStrategies\IpDashApiDotComStrategy as IpDashApiDotComRetryStrategy;
use XbNz\Resolver\Domain\Ip\Strategies\RetryStrategies\IpDataDotCoStrategy as IpDataDotCoRetryStrategy;
use XbNz\Resolver\Domain\Ip\Strategies\RetryStrategies\IpGeolocationDotIoStrategy as IpGeolocationDotIoRetryStrategy;
use XbNz\Resolver\Domain\Ip\Strategies\RetryStrategies\MtrDotShMtrStrategy as MtrDotShMtrRetryStrategy;
use XbNz\Resolver\Domain\Ip\Strategies\RetryStrategies\MtrDotShPingStrategy as MtrDotShPingRetryStrategy;

use XbNz\Resolver\Factories\GuzzleClientFactory;
use XbNz\Resolver\Factories\MappedResultFactory;

class ResolverServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/resolver.php', 'resolver');

        $this->app->tag([
            IpGeolocationDotIoAuthStrategy::class,
            IpDataDotCoAuthStrategy::class,
            AbuseIpDbDotComAuthStrategy::class,
            IpApiDotComAuthStrategy::class,
            IpInfoDotIoAuthStrategy::class,
            AbstractApiDotComAuthStrategy::class,
        ], 'auth-strategies');

        $this->app->tag([
            IpGeolocationDotIoRetryStrategy::class,
            IpDataDotCoRetryStrategy::class,
            AbuseIpDbDotComRetryStrategy::class,
            IpApiDotComRetryStrategy::class,
            MtrDotShMtrRetryStrategy::class,
            MtrDotShPingRetryStrategy::class,
            IpDashApiDotComRetryStrategy::class,
            IpApiDotCoRetryStrategy::class,
            AbstractApiDotComRetryStrategy::class,
        ], 'retry-strategies');

        $this->app->tag([
            MtrDotShMtrFormatterStrategy::class,
            IpApiDotComFormatterStrategy::class,
            MtrDotShPingFormatterStrategy::class,
        ], 'response-formatters');

        $this->app->tag([
            IpGeolocationDotIoMapper::class,
            IpDataDotCoMapper::class,
            AbuseIpDbDotComMapper::class,
            MtrDotShMtrMapper::class,
            IpApiDotComMapper::class,
            MtrDotShPingMapper::class,
            IpInfoDotIoMapper::class,
            IpDashApiDotComMapper::class,
            IpApiDotCoMapper::class,
            AbstractApiDotComMapper::class,
        ], 'mappers');

        $this->app->when(GuzzleClientFactory::class)
            ->needs('$authStrategies')
            ->giveTagged('auth-strategies');

        $this->app->when(GuzzleClientFactory::class)
            ->needs('$retryStrategies')
            ->giveTagged('retry-strategies');

        $this->app->when(GuzzleClientFactory::class)
            ->needs('$responseFormatters')
            ->giveTagged('response-formatters');

        $this->app->when(MappedResultFactory::class)
            ->needs('$mappers')
            ->giveTagged('mappers');
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../config/resolver.php' =>
                    config_path('resolver.php'),
            ], 'resolver');
        }
    }
}
