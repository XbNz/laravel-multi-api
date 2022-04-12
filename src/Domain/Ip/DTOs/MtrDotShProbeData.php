<?php

namespace XbNz\Resolver\Domain\Ip\DTOs;

use Webmozart\Assert\Assert;

class MtrDotShProbeData
{
    public function __construct(
        public readonly string $probeId,
        public readonly ?int $asNumber,
        public readonly ?string $city,
        public readonly ?string $country,
        public readonly ?string $continent,
        public readonly ?string $provider,
        public readonly ?string $providerUrl,
        public readonly ?string $unLoCode,

        public readonly bool $canPerformMtr = false,
        public readonly bool $canPerformDnsTrace = false,
        public readonly bool $canPerformTraceroute = false,
        public readonly bool $canPerformDnsResolve = false,
        public readonly bool $canPerformPing = false,
        public readonly bool $isOnline = false,
        public readonly bool $residential = false,
        public readonly bool $supportsVersion4 = false,
        public readonly bool $supportsVersion6 = false,
    ) {
        Assert::nullOrGreaterThanEq($asNumber, 0);
    }
}