<?php

namespace XbNz\Resolver\Domain\Ip\Strategies;

class NullStrategy
{
    public function guzzleMiddleware(...$gibberish): void
    {

    }

    public function supports(...$gibberish): bool
    {
        return true;
    }

}