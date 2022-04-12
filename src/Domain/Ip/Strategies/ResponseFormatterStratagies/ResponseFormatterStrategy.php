<?php

namespace XbNz\Resolver\Domain\Ip\Strategies\ResponseFormatterStratagies;

interface ResponseFormatterStrategy
{
    public function guzzleMiddleware(): callable;
    public function supports(string $driver): bool;
}