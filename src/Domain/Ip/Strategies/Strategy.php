<?php

namespace XbNz\Resolver\Domain\Ip\Strategies;

interface Strategy
{
    public function guzzleMiddleware(): callable;
    public function supports(string $apiBaseUri): bool;
}