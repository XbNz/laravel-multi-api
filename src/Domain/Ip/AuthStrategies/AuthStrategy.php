<?php

namespace XbNz\Resolver\Domain\Ip\AuthStrategies;

interface AuthStrategy
{
    public function guzzleMiddleware(): callable;
    public function supports(string $apiBaseUri): bool;
}