<?php

declare(strict_types=1);

namespace XbNz\Resolver\Domain\Ip\DTOs\MtrDotSh;

use Illuminate\Support\Collection;
use Webmozart\Assert\Assert;
use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\DTOs\MtrDotToolsProbeData;
use XbNz\Resolver\Support\DTOs\Mappable;

class MtrResultData
{
    /**
     * @param Collection<MtrDotShMtrHopResultsData> $hops
     */
    public function __construct(
        public readonly MtrDotToolsProbeData $probe,
        public readonly IpData $targetIp,
        public readonly Collection $hops,
    ) {
        Assert::allIsInstanceOf($hops->toArray(), MtrDotShMtrHopResultsData::class);
    }

    public static function fromRaw(array $raw, )
}
