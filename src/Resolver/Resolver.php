<?php

namespace XbNz\Resolver\Resolver;

use XbNz\Resolver\Domain\Ip\Builders\IpBuilder;

class Resolver
{
    public function __construct(
        private IpBuilder $ipBuilder
    )
    {}

    public function ip(): IpBuilder
    {
        return $this->ipBuilder;
    }
}