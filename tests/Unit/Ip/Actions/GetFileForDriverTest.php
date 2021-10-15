<?php

namespace XbNz\Resolver\Tests\Unit\Ip\Actions;

use XbNz\Resolver\Domain\Ip\Actions\GetFileForDriverAction;
use XbNz\Resolver\Domain\Ip\Drivers\Driver;

class GetFileForDriverTest extends \XbNz\Resolver\Tests\TestCase
{
    /** @test */
    public function it_fetches_the_file_for_a_driver()
    {
//        \File::makeDirectory(storage_path('Resolver'));
//        \File::makeDirectory(storage_path('Resolver/Ip'));

        \Config::set('ip-resolver.files', [
            'ipDb' => storage_path(),
        ]);

        $driverMock = $this->createMock(Driver::class);
        $driverMock->method('supports')->willReturn('ipDb');

        \File::put(storage_path("Resolver/Ip/{$driverMock->supports()}"), 'test');

        $key = app(GetFileForDriverAction::class)
            ->execute($driverMock);

    }

    protected function tearDown(): void
    {
        parent::tearDown();
        \File::deleteDirectory(storage_path('Resolver\Ip'));
    }
}