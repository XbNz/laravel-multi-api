<?php

namespace XbNz\Resolver\Domain\Ip\Strategies\AuthStrategies;

interface AuthStrategy
{
    public function guzzleMiddleware(): callable;
    public function supports(string $apiBaseUri): bool;
}