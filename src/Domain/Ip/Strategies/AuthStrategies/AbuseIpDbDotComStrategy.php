<?php

declare(strict_types=1);

namespace XbNz\Resolver\Domain\Ip\Strategies\AuthStrategies;

use Psr\Http\Message\RequestInterface;
use XbNz\Resolver\Domain\Ip\Drivers\AbuseIpDbDotComDriver;
use XbNz\Resolver\Support\Actions\GetRandomApiKeyAction;
use XbNz\Resolver\Support\Strategies\AuthStrategy;

class AbuseIpDbDotComStrategy implements AuthStrategy
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
                $randomKey = $getRandomApiKey->execute(AbuseIpDbDotComDriver::class, 'ip-resolver.api-keys');
                $request = $request->withHeader('key', $randomKey);

                return $handler($request, $options);
            };
        };
    }

    public function supports(string $service): bool
    {
//        return $service ===
    }
}
