<?php

declare(strict_types=1);

namespace XbNz\Resolver\Support\DTOs;

use Webmozart\Assert\Assert;
use XbNz\Resolver\Support\Drivers\Driver;

class RawResultsData
{
    /**
     * @param array<mixed> $data
     * @param class-string<Driver> $provider
     */
    public function __construct(
        public readonly string $provider,
        public readonly array $data,
    ) {
        Assert::classExists($provider);
    }
}
