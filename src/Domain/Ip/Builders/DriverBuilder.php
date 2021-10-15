<?php

namespace XbNz\Resolver\Domain\Ip\Builders;

use XbNz\Resolver\Domain\Ip\Actions\CollectEligibleDriversAction;
use XbNz\Resolver\Domain\Ip\DTOs\IpData;

class DriverBuilder
{
    public function __construct(CollectEligibleDriversAction $driversAction)
    {

    }

    public function __call(string $name, array $arguments)
    {
        // TODO: Implement __call() method.
    }

    public static function fromDto(IpData $ipData): self
    {

    }
}