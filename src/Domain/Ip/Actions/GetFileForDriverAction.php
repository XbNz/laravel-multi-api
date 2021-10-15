<?php

namespace XbNz\Resolver\Domain\Ip\Actions;

use phpDocumentor\Reflection\File;
use XbNz\Resolver\Domain\Ip\Drivers\Driver;
use XbNz\Resolver\Support\Exceptions\ConfigNotFoundException;
use XbNz\Resolver\Support\Exceptions\FileNotFoundException;

class GetFileForDriverAction
{
    public function execute(Driver $driver)
    {
        if (
            ! \Config::has("ip-resolver.files.{$driver->supports()}")
            ||
            ! is_string(config("ip-resolver.files.{$driver->supports()}"))
        ){
            throw new ConfigNotFoundException(
                "files.{$driver->supports()} not found in config or not compatible"
            );
        }

        $filePath = config("ip-resolver.files.{$driver->supports()}");

        dd(\File::get($filePath));
        $file = \File::exists($filePath)
            ?
            \File::get($filePath)
            :
            throw new FileNotFoundException(
                "Could not find {$filePath}. Please check your config file"
            );
    }
}