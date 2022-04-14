<?php

namespace XbNz\Resolver\Support\Strategies;

interface ResponseFormatterStrategy
{
    public function guzzleMiddleware(): callable;
    public function supports(string $driver): bool;
}