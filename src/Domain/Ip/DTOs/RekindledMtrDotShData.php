<?php

namespace XbNz\Resolver\Domain\Ip\DTOs;

use Webmozart\Assert\Assert;

class RekindledMtrDotShData
{
    public function __construct(
        public readonly string $plainTextBody,
        public readonly string $probeId,
        public readonly string $ip,
    ) {
        Assert::ip($ip);
    }
}