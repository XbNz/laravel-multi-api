<?php

declare(strict_types=1);

namespace XbNz\Resolver\Domain\Ip\DTOs\MtrDotSh;

use Illuminate\Support\Collection;
use Webmozart\Assert\Assert;
use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Support\DTOs\MappableDTO;

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
        Assert::allIsInstanceOf($hops->toArray(), MtrDotShMtrHopResultsData::class);
    }
}
