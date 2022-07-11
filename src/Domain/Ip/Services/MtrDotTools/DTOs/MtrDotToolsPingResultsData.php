<?php

namespace XbNz\Resolver\Domain\Ip\Services\MtrDotTools\DTOs;

use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Collections\PingSequenceCollection;

class MtrDotToolsPingResultsData
{
    /**
     * @param array<MtrDotToolsPingSequenceResultsData> $sequences
     */
    public function __construct(
        public readonly MtrDotToolsProbeData $probe,
        public readonly IpData $targetIp,
        public readonly float $packetLossPercentage,
        public readonly array $sequences,
        public readonly ?MtrDotToolsPingStatisticsResultsData $statistics,
    ) {
    }
}