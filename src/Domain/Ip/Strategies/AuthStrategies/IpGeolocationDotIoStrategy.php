<?php

declare(strict_types=1);

namespace XbNz\Resolver\Domain\Ip\Strategies\AuthStrategies;

use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\RequestInterface;
use XbNz\Resolver\Domain\Ip\Drivers\IpGeolocationDotIoDriver;
use XbNz\Resolver\Support\Actions\GetRandomApiKeyAction;
use XbNz\Resolver\Support\Strategies\AuthStrategy;

class IpGeolocationDotIoStrategy implements AuthStrategy
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
                $uri = $request->getUri();

                $randomKey = $getRandomApiKey->execute(IpGeolocationDotIoDriver::class, 'ip-resolver.api-keys');

                $newUri = Uri::withQueryValue($uri, 'apiKey', $randomKey);
                $request = $request->withUri($newUri);

                return $handler($request, $options);
            };
        };
    }

    public function supports(string $service): bool
    {
//        return $service ===
    }
}
