<?php

namespace XbNz\Resolver\Tests\Unit\Support\Actions;

use XbNz\Resolver\Factories\Ip\GuzzleIpClientFactory;
use XbNz\Resolver\Support\Actions\MakeHttpPromiseAction;
use XbNz\Resolver\Support\Drivers\Driver;

class MakeHttpPromiseTest extends \XbNz\Resolver\Tests\TestCase
{
    private Driver $driver;

    /** @test **/
    public function example(): void
    {
        // Arrange
        $promise = app(MakeHttpPromiseAction::class);
        $factory = app(GuzzleIpClientFactory::class);


        // Act
        $promise = $promise->execute(
            $factory->for('1.1.1.1', 'ipgeolocation.io')
        );


        // Assert
        dd($promise->wait()->getBody()->getContents());
    }

}