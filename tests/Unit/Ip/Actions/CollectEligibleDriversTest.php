<?php

namespace XbNz\Resolver\Tests\Unit\Ip\Actions;

use XbNz\Resolver\Domain\Ip\Actions\CollectEligibleDriversAction;
use XbNz\Resolver\Domain\Ip\Drivers\IpInfoDriver;

class CollectEligibleDriversTest extends \XbNz\Resolver\Tests\TestCase
{
    /** @test */
    public function it_receives_the_tagged_drivers_from_ioc_and_filters_those_with_no_key_in_the_config()
    {
        \Config::set('ip-resolver.api-keys', [
            'ipApi' => 'something-that-looks-like-an-api-key',
            'ipGeolocation' => 'something-that-looks-like-an-api-key',
            'ipInfo' => null,
        ]);

//        \File::makeDirectory(storage_path('Resolver'));

        \Config::set('ip-resolver.files', [
            'ipDb' => storage_path()
        ]);

        $driverCollection = app(CollectEligibleDriversAction::class)
            ->execute();

        $this->assertCount(2, $driverCollection);
        $this->assertNotInstanceOf($driverCollection[0], IpInfoDriver::class);
        $this->assertNotInstanceOf($driverCollection[1], IpInfoDriver::class);
    }


    protected function tearDown(): void
    {
        parent::tearDown();
        \File::deleteDirectory(storage_path('Resolver'));
    }
}