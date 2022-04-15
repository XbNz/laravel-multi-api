<?php

declare(strict_types=1);

namespace XbNz\Resolver\Support\Strategies;

interface AuthStrategy
{
    public function guzzleMiddleware(): callable;

    public function supports(string $driver): bool;
}
