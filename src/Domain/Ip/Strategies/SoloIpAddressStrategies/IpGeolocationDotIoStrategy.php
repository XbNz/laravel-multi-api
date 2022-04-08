<?php

namespace XbNz\Resolver\Domain\Ip\Strategies\SoloIpAddressStrategies;

use GuzzleHttp\Psr7\Uri;
use Illuminate\Support\Str;
use Psr\Http\Message\RequestInterface;
use XbNz\Resolver\Domain\Ip\Strategies\Strategy;

class IpGeolocationDotIoStrategy implements Strategy
{

    public function guzzleMiddleware(): callable
    {
        return static function (callable $handler) {
            return function (RequestInterface $request, array $options) use ($handler) {
                $request->withUri(new Uri('https://api.ipgeolocation.io/ipgeo'));

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