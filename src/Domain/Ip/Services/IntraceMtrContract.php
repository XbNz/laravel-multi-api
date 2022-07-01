<?php

namespace XbNz\Resolver\Domain\Ip\Services;

use XbNz\Resolver\Domain\Ip\DTOs\MtrDotSh\MtrDotShProbeData;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\DTOs\MtrDotToolsProbeData;

interface IntraceMtrContract
{
    public function listProbes(): MtrDotShProbeData|MtrDotToolsProbeData;
    public function probe(string $probeId): MtrDotShProbeData;
}