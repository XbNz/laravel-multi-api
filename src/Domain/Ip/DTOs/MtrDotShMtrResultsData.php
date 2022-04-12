<?php

namespace XbNz\Resolver\Domain\Ip\DTOs;

use Illuminate\Support\Collection;

class MtrDotShMtrResultsData
{
    /**
     * @param Collection<MtrDotShMtrHopResultsData> $hops
     */
    public function __construct(
        public readonly MtrDotShProbeData $probe,
        public readonly IpData $targetIp,
        public readonly Collection $hops,
    ) {}
}