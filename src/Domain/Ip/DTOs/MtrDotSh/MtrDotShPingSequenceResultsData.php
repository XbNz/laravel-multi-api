<?php

declare(strict_types=1);

namespace XbNz\Resolver\Domain\Ip\DTOs\MtrDotSh;

use Webmozart\Assert\Assert;

class MtrDotShPingSequenceResultsData
{
    public function __construct(
        public readonly int $size,
        public readonly string $ip,
        public readonly int $sequenceNumber,
        public readonly int $timeToLive,
        public readonly float $roundTripTime,
    ) {
        Assert::ip($ip);
    }
}
