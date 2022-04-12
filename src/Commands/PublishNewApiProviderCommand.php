<?php

namespace XbNz\Resolver\Commands;

use Illuminate\Console\Command;

class PublishNewApiProviderCommand extends Command
{
    protected $signature = 'make:api-provider';

    protected $description = 'Generate a new set of driver, Guzzle strategy middlewares and mapper classes to support a new API provider';

    public function handle()
    {
        //
    }
}
