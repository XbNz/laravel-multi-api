<?php

declare(strict_types=1);

namespace XbNz\Resolver\Domain\Ip\Strategies\AuthStrategies;

use Psr\Http\Message\RequestInterface;
use XbNz\Resolver\Domain\Ip\Drivers\IpInfoDotIoDriver;
use XbNz\Resolver\Support\Actions\GetRandomApiKeyAction;
use XbNz\Resolver\Support\Strategies\AuthStrategy;

class IpInfoDotIoStrategy implements AuthStrategy
{
    public function __construct(
        private GetRandomApiKeyAction $getRandomApiKey,
    ) {
    }

    public function guzzleMiddleware(): callable
    {
        $getRandomApiKey = $this->getRandomApiKey;
        return static function (callable $handler) use ($getRandomApiKey) {
            return static function (RequestInterface $request, array $options) use ($handler, $getRandomApiKey) {
                $randomKey = $getRandomApiKey->execute(IpInfoDotIoDriver::class, 'ip-resolver.api-keys');
                $request = $request->withHeader('Authorization', "Bearer {$randomKey}");
                return $handler($request, $options);
            };
        };
    }

    public function supports(string $driver): bool
    {
        return $driver === IpInfoDotIoDriver::class;
    }
}