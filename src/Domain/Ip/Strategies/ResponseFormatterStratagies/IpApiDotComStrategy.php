<?php

declare(strict_types=1);

namespace XbNz\Resolver\Domain\Ip\Strategies\ResponseFormatterStratagies;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use XbNz\Resolver\Domain\Ip\Drivers\IpApiDotComDriver;
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
                    function (ResponseInterface $response) {
                        $json = json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);

                        if (array_key_exists('success', $json) && $json['success'] === false) {
                            $response = $response->withStatus(401, $json['error']['type'] ?? '');
                        }

                        return $response;
                    }
                );
            };
        };
    }

    public function supports(string $driver): bool
    {
        return $driver === IpApiDotComDriver::class;
    }
}
