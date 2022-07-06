<?php

namespace XbNz\Resolver\Domain\Ip\Services\MtrDotTools\DTOs;

use Webmozart\Assert\Assert;
use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Collections\HopCollection;

class MtrDotToolsMtrResultsData
{

    /**
     * @param HopCollection<MtrDotToolsHopData> $hops
     */
    public function __construct(
        public readonly MtrDotToolsProbeData $probe,
        public readonly IpData $targetIp,
        public readonly HopCollection $hops,
    ) {
        Assert::allIsInstanceOf($hops->toArray(), MtrDotToolsHopData::class);
    }
}