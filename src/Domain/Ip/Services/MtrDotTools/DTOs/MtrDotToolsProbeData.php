<?php

declare(strict_types=1);

namespace XbNz\Resolver\Domain\Ip\Services\MtrDotTools\DTOs;

use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Enums\MTR;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Enums\Ping;

class MtrDotToolsProbeData
{
    public function __construct(
        public readonly string $probeId,
        public readonly MTR $mtr,
        public readonly Ping $ping,
        public readonly ?int $asNumber,
        public readonly ?string $city,
        public readonly ?string $country,
        public readonly ?string $continent,
        public readonly ?string $provider,
        public readonly ?string $providerUrl,
        public readonly ?string $unLoCode,
        public readonly ?bool $isOnline = false,
        public readonly ?bool $residential = false,
    ) {
    }

    public static function fromRaw(array $raw, string $probeId): self
    {
        $mtr = match ($raw['caps']['mtr'] ?? false) {
            true => MTR::OnBothIpVersions,
            4 => MTR::OnIpVersion4,
            6 => MTR::OnIpVersion6,
            default => MTR::Incapable,
        };

        $ping = match ($raw['caps']['ping'] ?? false) {
            true => Ping::OnBothIpVersions,
            4 => Ping::OnIpVersion4,
            6 => Ping::OnIpVersion6,
            default => Ping::Incapable,
        };

        return new self(
            $probeId,
            $mtr,
            $ping,
            $raw['asnumber'] ?? null,
            $raw['city'] ?? null,
            $raw['country'] ?? null,
            $raw['group'] ?? null,
            $raw['provider'] ?? null,
            $raw['providerurl'] ?? null,
            $raw['unlocode'] ?? null,
            $raw['status'] ?? false,
            $raw['residential'] ?? false,
        );
    }
}
