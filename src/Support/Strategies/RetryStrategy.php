<?php

namespace XbNz\Resolver\Support\Strategies;

interface RetryStrategy
{
    public function guzzleMiddleware(): callable;
    public function supports(string $driver): bool;
}