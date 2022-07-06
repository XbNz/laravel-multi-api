<?php

namespace XbNz\Resolver\Domain\Ip\Services\MtrDotTools\DTOs;

use XbNz\Resolver\Domain\Ip\DTOs\MtrDotSh\MtrDotShMtrHopResultsData;

class MtrDotToolsHopData
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

    public static function fromRaw(array $hop, int $positionKey): self
    {
        return new self(
            $positionKey,
            $hop['hop_host'],
            (float) $hop['statistics']['Loss%'],
            (int) $hop['statistics']['Drop'],
            (int) $hop['statistics']['Rcv'],
            (int) $hop['statistics']['Snt'],
            (float) $hop['statistics']['Last'],
            (float) $hop['statistics']['Best'],
            (float) $hop['statistics']['Avg'],
            (float) $hop['statistics']['Wrst'],
            (float) $hop['statistics']['StDev'],
            (float) $hop['statistics']['Gmean'],
            (float) $hop['statistics']['Jttr'],
            (float) $hop['statistics']['Javg'],
            (float) $hop['statistics']['Jmax'],
            (float) $hop['statistics']['Jint'],
        );
    }
}