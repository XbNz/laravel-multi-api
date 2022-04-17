<?php

declare(strict_types=1);

namespace XbNz\Resolver\Support\DTOs;

use Webmozart\Assert\Assert;

class RawResultsData
{
    /**
     * @param array<mixed> $data
     */
    public function __construct(
        public readonly string $provider,
        public readonly array $data,
    ) {
        Assert::classExists($provider);
    }
}
