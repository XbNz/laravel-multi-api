<?php

namespace XbNz\Resolver\Domain\Ip\DTOs;


use XbNz\Resolver\Domain\Ip\Actions\VerifyIpIntegrityAction;

class IpData
{

    public function __construct(
        public readonly string $ip,
        public readonly int $version,
    ) {
    }
}