<?php

declare(strict_types=1);

namespace XbNz\Resolver\Support\Strategies;

interface RetryStrategy
{
    public function guzzleMiddleware(): callable;

    public function supports(string $service): bool;
}
