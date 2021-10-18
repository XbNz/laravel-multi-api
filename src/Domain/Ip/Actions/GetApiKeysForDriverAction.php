<?php

namespace XbNz\Resolver\Domain\Ip\Actions;

use Illuminate\Support\Collection;
use XbNz\Resolver\Support\Drivers\Driver;
use XbNz\Resolver\Support\Exceptions\ConfigNotFoundException;

class GetApiKeysForDriverAction
{
    public function execute(Driver $driver): array
    {
        if (
            ! \Config::has("ip-resolver.api-keys.{$driver->supports()}")
            ||
            ! is_array(config("ip-resolver.api-keys.{$driver->supports()}"))
        ){
            throw new ConfigNotFoundException(
                "api-keys.{$driver->supports()} not found in config or not compatible"
            );
        }

        return config("ip-resolver.api-keys.{$driver->supports()}");
    }
}