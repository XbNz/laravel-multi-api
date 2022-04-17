<?php

namespace XbNz\Resolver\Domain\Ip\Strategies\ResponseFormatterStratagies;

use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use XbNz\Resolver\Domain\Ip\Actions\ConvertPingPlainToJsonAction;
use XbNz\Resolver\Domain\Ip\Drivers\MtrDotShPingDriver;
use XbNz\Resolver\Factories\Ip\RekindledMtrDotShFactory;
use XbNz\Resolver\Support\Strategies\ResponseFormatterStrategy;

class MtrDotShPingStrategy implements ResponseFormatterStrategy
{
    public function __construct(
        private ConvertPingPlainToJsonAction $plainToJsonAction
    ) {
    }

    public function guzzleMiddleware(): callable
    {
        $plainToJsonAction = $this->plainToJsonAction;
        return static function (callable $handler) use ($plainToJsonAction) {
            return static function (
                RequestInterface $request,
                array $options
            ) use ($handler, $plainToJsonAction) {
                $promise = $handler($request, $options);
                return $promise->then(
                    function (ResponseInterface $response) use ($request, $plainToJsonAction) {
                        $json = $plainToJsonAction->execute(
                            RekindledMtrDotShFactory::fromResponseAndRequest($response, $request)
                        );

                        $response = $response->withHeader('Content-Type', 'application/json');

                        return $response->withBody(Utils::streamFor($json));
                    }
                );
            };
        };
    }

    public function supports(string $driver): bool
    {
        return $driver === MtrDotShPingDriver::class;
    }
}