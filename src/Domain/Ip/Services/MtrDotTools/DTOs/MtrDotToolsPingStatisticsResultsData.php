<?php

namespace XbNz\Resolver\Domain\Ip\Services\MtrDotTools\DTOs;

class MtrDotToolsPingStatisticsResultsData
{
    public function __construct(
        public readonly float $minimumRoundTripTime,
        public readonly float $averageRoundTripTime,
        public readonly float $maximumRoundTripTime,
        public readonly float $jitter,
    ) {
    }
}