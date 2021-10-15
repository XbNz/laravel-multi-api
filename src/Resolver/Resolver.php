<?php

namespace XbNz\Resolver\Resolver;

use XbNz\Resolver\Domain\Ip\Actions\VerifyIpIntegrityAction;
use XbNz\Resolver\Domain\Ip\Builders\DriverBuilder;

class Resolver
{
    public function __construct(VerifyIpIntegrityAction $verifyIpIntegrity)
    {}

    public function ip(string $ip): DriverBuilder
    {

    }
}