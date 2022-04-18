<?php

declare(strict_types=1);

namespace XbNz\Resolver\Domain\Ip\DTOs\MtrDotSh;

class MtrDotShPingStatisticsResultsData
{
    public function __construct(
        public readonly float $minimumRoundTripTime,
        public readonly float $averageRoundTripTime,
        public readonly float $maximumRoundTripTime,
        public readonly float $jitter,
    ) {
    }
}
