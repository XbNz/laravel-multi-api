<?php

namespace XbNz\Resolver\Resolver;

use XbNz\Resolver\Domain\Ip\Actions\VerifyIpIntegrityAction;
use XbNz\Resolver\Domain\Ip\Builders\DriverBuilder;

class Resolver
{
    public function __construct(
        private DriverBuilder $driverBuilder
    )
    {}

    public function ip(): DriverBuilder
    {
        return $this->driverBuilder;
    }
}