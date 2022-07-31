<?php

declare(strict_types=1);

namespace XbNz\Resolver\Domain\Ip\Services\MtrDotTools\DTOs;

use XbNz\Resolver\Domain\Ip\DTOs\IpData;

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
