<?php

declare(strict_types=1);

namespace XbNz\Resolver\Support\Guzzle\Middlewares;

use Closure;
use Psr\Http\Message\RequestInterface;

class WithProxy
{
    public function __invoke(string $proxy): Closure
    {
        return static function (callable $handler) use ($proxy) {
            return static function (RequestInterface $request, array $options) use ($handler, $proxy) {
                $options['proxy'] = $proxy;
                return $handler($request, $options);
            };
        };
    }
}
