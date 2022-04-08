<?php

namespace XbNz\Resolver\Tests\Unit\Support\Actions;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Support\Actions\MakeHttpPromiseAction;
use XbNz\Resolver\Support\Drivers\Driver;
use XbNz\Resolver\Support\Exceptions\ApiProviderException;
use XbNz\Resolver\Support\Factories\GuzzleConfigFactory;

class MakeHttpPromiseTest extends \XbNz\Resolver\Tests\TestCase
{
    private Driver $driver;

    /** @test **/
    public function example(): void
    {
        // Arrange
        $action = app(MakeHttpPromiseAction::class);

        // Act
        $promise = $action->execute(GuzzleConfigFactory::forIpGeolocationDotIo(new IpData(ip: '1.1.1.1', version: 4)));


        // Assert
        dd($promise->wait()->getBody()->getContents());
    }

}