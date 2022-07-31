<?php

declare(strict_types=1);

namespace XbNz\Resolver\Domain\Ip\Services\MtrDotTools\DTOs;

use Webmozart\Assert\Assert;

class MtrDotToolsPingSequenceResultsData
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

    public static function fromRaw(array $sequence): self
    {
        return new self(
            $sequence['size'],
            $sequence['ip'],
            $sequence['sequence_number'],
            $sequence['time_to_live'],
            $sequence['rtt'],
        );
    }
}
