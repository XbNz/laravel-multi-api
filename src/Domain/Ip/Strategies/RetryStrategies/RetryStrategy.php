<?php

namespace XbNz\Resolver\Domain\Ip\Strategies\RetryStrategies;

interface RetryStrategy
{
    public function guzzleMiddleware(): callable;
    public function supports(string $apiBaseUri): bool;
}