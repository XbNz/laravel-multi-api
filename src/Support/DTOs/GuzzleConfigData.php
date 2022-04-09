<?php

namespace XbNz\Resolver\Support\DTOs;

use Closure;
use Psr\Http\Message\RequestInterface;
use Webmozart\Assert\Assert;

class GuzzleConfigData
{
    /**
     * @param ?array<callable> $middlewares
     */
    public function __construct(
        public readonly ?array $middlewares = null
    ) {
        Assert::allIsCallable($middlewares);
    }
}