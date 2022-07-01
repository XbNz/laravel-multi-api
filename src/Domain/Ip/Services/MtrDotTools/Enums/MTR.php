<?php

namespace XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Enums;

enum MTR
{
    case OnIpVersion4;
    case OnIpVersion6;
    case OnBothIpVersions;
    case Incapable;


    public function compatibleWith(IpVersion $ipVersion): bool
    {
        return match ($ipVersion) {
            IpVersion::FOUR => $this === self::OnIpVersion4 || $this === self::OnBothIpVersions,
            IpVersion::SIX => $this === self::OnIpVersion6 || $this === self::OnBothIpVersions,
            default => false
        };
    }
}