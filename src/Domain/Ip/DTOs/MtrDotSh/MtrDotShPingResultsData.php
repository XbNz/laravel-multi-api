<?php

declare(strict_types=1);

namespace XbNz\Resolver\Domain\Ip\DTOs\MtrDotSh;

use Illuminate\Support\Collection;
use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Support\DTOs\MappableDTO;

class MtrDotShPingResultsData implements MappableDTO
{
    /**
     * @param Collection<MtrDotShPingSequenceResultsData> $sequences
     */
    public function __construct(
        public readonly MtrDotShProbeData $probe,
        public readonly IpData $targetIp,
        public readonly float $packetLossPercentage,
        public readonly Collection $sequences,
        public readonly ?MtrDotShPingStatisticsResultsData $statistics,
    ) {
    }
}
