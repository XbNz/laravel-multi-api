<?php

namespace XbNz\Resolver\Support\DTOs;

use Psr\Http\Message\RequestInterface;

class GuzzleConfigData
{
    /**
     * @param ?array<callable> $middlewares
     */
    public function __construct(
        public readonly string $baseUri,
        public readonly RequestInterface $request,
        public readonly ?array $queryParams = null,
        public readonly ?array $middlewares = null
    ) {}
}