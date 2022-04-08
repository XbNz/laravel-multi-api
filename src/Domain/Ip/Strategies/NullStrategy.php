<?php

namespace XbNz\Resolver\Domain\Ip\Strategies;

class NullStrategy
{
    public function guzzleMiddleware(...$gibberish): callable
    {
        return static function () {};
    }

    public function supports(...$gibberish): bool
    {
        return true;
    }

}