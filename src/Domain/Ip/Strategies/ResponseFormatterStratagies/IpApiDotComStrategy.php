<?php

declare(strict_types=1);

namespace XbNz\Resolver\Domain\Ip\Strategies\ResponseFormatterStratagies;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use XbNz\Resolver\Support\Strategies\ResponseFormatterStrategy;

class IpApiDotComStrategy implements ResponseFormatterStrategy
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
                    function (ResponseInterface $response) use ($request) {
                        $json = json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);
                        if ($json['success'] === false) {
                            $response = $response->withStatus(403);
                        }

                        return $response;
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
