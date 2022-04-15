<?php

declare(strict_types=1);

namespace XbNz\Resolver\Support\Guzzle\Middlewares;

use Closure;
use Psr\Http\Message\RequestInterface;

class WithTimeout
{
    public function __invoke(float $timeout = 5): Closure
    {
        return static function (callable $handler) use ($timeout) {
            return static function (RequestInterface $request, array $options) use ($handler, $timeout) {
                $options['timeout'] = $timeout;
                return $handler($request, $options);
            };
        };
    }
}
