<?php

declare(strict_types=1);

namespace XbNz\Resolver\Domain\Ip\DTOs\MtrDotSh;

use Webmozart\Assert\Assert;

class RekindledMtrData
{
    public function __construct(
        public readonly string $plainTextBody,
        public readonly string $probeId,
        public readonly string $ip,
    ) {
        Assert::ip($ip);
    }
}
