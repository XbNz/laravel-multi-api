<?php

namespace XbNz\Resolver\Domain\Ip\Actions;

use Illuminate\Filesystem\Filesystem;
use phpDocumentor\Reflection\File;
use XbNz\Resolver\Support\Drivers\Driver;
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

        $file = \File::exists($filePath)
            ?
            $filePath
            :
            throw new FileNotFoundException(
                "Could not find {$filePath}. Please check your config file"
            );

        return $file;
    }
}