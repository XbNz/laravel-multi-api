<?php

namespace XbNz\Resolver\Domain\Ip\Strategies\ResponseFormatterStratagies;

use Illuminate\Support\Str;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use XbNz\Resolver\Domain\Ip\Drivers\MtrDotShMtrDriver;

class MtrDotShMtrStrategy implements ResponseFormatterStrategy
{

    public function guzzleMiddleware(): callable
    {
        return static function (callable $handler) {
            return static function (
                RequestInterface $request,
                array $options
            ) use ($handler) {
                $promise = $handler($request, $options);
                return $promise->then(
                    function (ResponseInterface $response) {
                        dd($response->getBody()->getContents());
                    }
                );
            };
        };
    }


    public function supports(string $driver): bool
    {
        return $driver === MtrDotShMtrDriver::class;
    }
}