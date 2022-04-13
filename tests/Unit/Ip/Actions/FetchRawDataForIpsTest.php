<?php

namespace XbNz\Resolver\Tests\Unit\Ip\Actions;

use Illuminate\Support\Collection;
use XbNz\Resolver\Domain\Ip\Actions\FetchRawDataForIpsAction;
use XbNz\Resolver\Domain\Ip\Builders\DriverBuilder;
use XbNz\Resolver\Domain\Ip\Drivers\AbuseIpDbDotComDriver;
use XbNz\Resolver\Domain\Ip\Drivers\IpGeolocationDotIoDriver;
use XbNz\Resolver\Domain\Ip\Drivers\MtrDotShMtrDriver;
use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Domain\Ip\DTOs\RawIpResultsData;
use XbNz\Resolver\Domain\Ip\Mappings\MtrDotShMtrMapper;
use XbNz\Resolver\Factories\Ip\MappedResultFactory;

class FetchRawDataForIpsTest extends \XbNz\Resolver\Tests\TestCase
{
    /** @test **/
    public function example(): void
    {

        $builder = app(DriverBuilder::class);
        $t = $builder
            ->withIps(['1.1.1.1'])
            ->mtrDotShMtr()
            ->normalize();

        dd($t);



        // Assert
    }
}