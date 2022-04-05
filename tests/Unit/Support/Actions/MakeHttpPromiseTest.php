<?php

namespace XbNz\Resolver\Tests\Unit\Support\Actions;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use XbNz\Resolver\Support\Actions\MakeHttpPromiseAction;
use XbNz\Resolver\Support\Drivers\Driver;
use XbNz\Resolver\Support\Exceptions\ApiProviderException;

class MakeHttpPromiseTest extends \XbNz\Resolver\Tests\TestCase
{
    private Driver $driver;

    protected function setUp(): void
    {
        $this->driver = $this->createMock(Driver::class);
        parent::setUp();
    }

    

}