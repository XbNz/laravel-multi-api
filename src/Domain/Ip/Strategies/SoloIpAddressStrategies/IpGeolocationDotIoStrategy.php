<?php

namespace XbNz\Resolver\Domain\Ip\Strategies\SoloIpAddressStrategies;

use GuzzleHttp\Psr7\Uri;
use Illuminate\Support\Str;
use Psr\Http\Message\RequestInterface;
use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Domain\Ip\Strategies\Strategy;

class IpGeolocationDotIoStrategy implements SoloIpStrategy
{

    public function guzzleMiddleware(IpData $ipData): callable
    {
        return static function (callable $handler) use ($ipData) {
            return static function (RequestInterface $request, array $options) use ($handler, $ipData) {
                $uri = $request->getUri();

                $request = $request
                    ->withMethod('GET')
                    ->withUri(new Uri('https://api.ipgeolocation.io/ipgeo/'));


                $newUri = Uri::withQueryValue($uri, 'ip', $ipData->ip);

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