<?php

declare(strict_types=1);

namespace XbNz\Resolver\Domain\Ip\DTOs;

class MtrDotShMtrHopResultsData
{
    public function __construct(
        public readonly int $hopPositionCount,
        public readonly string $hopHost,
        public readonly float $packetLossPercentage,
        public readonly int $droppedPackets,
        public readonly int $receivedPackets,
        public readonly int $sentPackets,
        public readonly float $lastRttValue,
        public readonly float $lowestRttValue,
        public readonly float $averageRttValue,
        public readonly float $highestRttValue,
        public readonly float $standardDeviation,
        public readonly float $geometricMean,
        public readonly float $jitter,
        public readonly float $averageJitter,
        public readonly float $maximumJitter,
        public readonly float $interarrivalJitter,
    ) {
    }
}
