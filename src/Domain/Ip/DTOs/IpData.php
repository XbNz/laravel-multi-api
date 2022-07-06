<?php

declare(strict_types=1);

namespace XbNz\Resolver\Domain\Ip\DTOs;

use InvalidArgumentException;
use Webmozart\Assert\Assert;
use XbNz\Resolver\Support\DTOs\DriverConsumableDTO;

class IpData implements DriverConsumableDTO
{
    public function __construct(
        public readonly string $ip,
        public readonly int $version,
    ) {
        $validated = filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE |
            FILTER_FLAG_NO_RES_RANGE
        );

        Assert::notFalse((bool) $validated, 'Invalid IP address');
    }

    public static function fromIp(string $ip): self
    {
        $v4 = filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_IPV4
        );

        $v6 = filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_IPV6
        );

        return match (true) {
            is_string($v4) => new self(ip: $ip, version: 4),
            is_string($v6) => new self(ip: $ip, version: 6),
            default => throw new InvalidArgumentException('Invalid IP address'),
        };
    }
}
