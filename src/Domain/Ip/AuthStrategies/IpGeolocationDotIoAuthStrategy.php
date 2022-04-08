<?php

namespace XbNz\Resolver\Domain\Ip\AuthStrategies;

use Illuminate\Support\Str;
use Psr\Http\Message\RequestInterface;

class IpGeolocationDotIoAuthStrategy implements AuthStrategy
{

    public function guzzleMiddleware(): callable
    {
        return static function (callable $handler) {
            return static function (RequestInterface $request, array $options) use ($handler) {

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