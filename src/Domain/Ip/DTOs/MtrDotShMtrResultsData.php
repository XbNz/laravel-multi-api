<?php

namespace XbNz\Resolver\Domain\Ip\DTOs;

use Illuminate\Support\Collection;
use Webmozart\Assert\Assert;

class MtrDotShMtrResultsData implements MappableDTO
{
    /**
     * @param Collection<MtrDotShMtrHopResultsData> $hops
     */
    public function __construct(
        public readonly MtrDotShProbeData $probe,
        public readonly IpData $targetIp,
        public readonly Collection $hops,
    ) {
        Assert::allIsInstanceOf($hops, MtrDotShMtrHopResultsData::class);
    }
}