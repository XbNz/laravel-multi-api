<?php

namespace XbNz\Resolver\Domain\Ip\Strategies\AuthStrategies;

use GuzzleHttp\Psr7\Uri;
use Illuminate\Support\Str;
use Psr\Http\Message\RequestInterface;
use XbNz\Resolver\Domain\Ip\Strategies\Strategy;
use XbNz\Resolver\Support\Actions\GetRandomApiKeyAction;

class IpGeolocationDotIoStrategy implements Strategy
{
    public function __construct(
        private GetRandomApiKeyAction $getRandomApiKey,
    )
    {}


    public function guzzleMiddleware(): callable
    {
        return static function (callable $handler) {
            return function (RequestInterface $request, array $options) use ($handler) {
                $uri = $request->getUri();
                $randomKey = $this->getRandomApiKey->execute($uri, 'ip-resolver.api-keys');

                $newUri = Uri::withQueryValue($uri, 'apiKey', $randomKey);
                $request->withUri($newUri);

                return $handler($request, $options);
            };
        };
    }

    public function supports(string $apiBaseUri): bool
    {
        return Str::of($apiBaseUri)
            ->lower()
            ->contains('ipgeolocation.io');
    }
}