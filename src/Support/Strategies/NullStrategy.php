<?php

declare(strict_types=1);

namespace XbNz\Resolver\Support\Strategies;

class NullStrategy
{
    public function guzzleMiddleware(mixed ...$gibberish): void
    {
    }

    public function supports(mixed ...$gibberish): bool
    {
        return true;
    }
}
