<?php

namespace XbNz\Resolver\Domain\Ip\Actions;

use Illuminate\Support\Collection;
use XbNz\Resolver\Domain\Ip\Drivers\Driver;
use XbNz\Resolver\Support\Exceptions\ConfigNotFoundException;

class GetApiKeyForDriverAction
{
    public function execute(Driver $driver): string
    {
        if (
            ! \Config::has("ip-resolver.api-keys.{$driver->supports()}")
            ||
            ! is_string(config("ip-resolver.api-keys.{$driver->supports()}"))
        ){
            throw new ConfigNotFoundException(
                "api-keys.{$driver->supports()} not found in config or not compatible"
            );
        }

        return config("ip-resolver.api-keys.{$driver->supports()}");

    }
}