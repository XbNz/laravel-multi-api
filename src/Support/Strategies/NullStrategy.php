<?php

namespace XbNz\Resolver\Support\Strategies;

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