<?php

declare(strict_types=1);

namespace XbNz\Resolver\Domain\Ip\DTOs;


use Webmozart\Assert\Assert;
use XbNz\Resolver\Domain\Ip\Actions\VerifyIpIntegrityAction;

class IpData
{

    public function __construct(
        public readonly string $ip,
        public readonly int $version,
    ) {
        $validated = filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE|
            FILTER_FLAG_NO_RES_RANGE
        );

        Assert::notFalse((bool) $validated, 'Invalid IP address');
    }
}