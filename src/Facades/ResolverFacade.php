<?php

declare(strict_types=1);

namespace XbNz\Resolver\Facades;

class ResolverFacade extends \Illuminate\Support\Facades\Facade
{
    protected static function getFacadeAccessor()
    {
        return app(\XbNz\Resolver\Resolver\Resolver::class);
    }
}
