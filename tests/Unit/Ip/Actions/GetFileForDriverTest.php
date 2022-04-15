<?php

namespace XbNz\Resolver\Tests\Unit\Ip\Actions;

use Illuminate\Filesystem\Filesystem;
use XbNz\Resolver\Domain\Ip\Actions\GetFileForDriverAction;
use XbNz\Resolver\Support\Drivers\Driver;
use XbNz\Resolver\Support\Exceptions\ConfigNotFoundException;
use XbNz\Resolver\Support\Exceptions\FileNotFoundException;

class GetFileForDriverTest extends \XbNz\Resolver\Tests\TestCase
{
    /** @test */
    public function it_fetches_the_file_for_a_driver()
    {
        \File::makeDirectory(storage_path('Resolver'));
        \File::makeDirectory(storage_path('Resolver/Ip'));

        \Config::set('ip-resolver.files', [
            'ipDb' => storage_path('Resolver/Ip/ipDb.csv'),
        ]);

        $driverMock = $this->createMock(Driver::class);
        $driverMock->method('supports')->willReturn('ipDb');

        \File::put(storage_path("Resolver/Ip/ipDb.csv"), 'test');
        $this->assertFileExists(storage_path("Resolver/Ip/ipDb.csv"));

        $filePath = app(GetFileForDriverAction::class)
            ->execute($driverMock);

        $this->assertStringEqualsFile($filePath, 'test');
    }

    /** @test */
    public function it_throws_a_config_not_found_error_then_key_is_not_set()
    {


        \Config::set('ip-resolver.files', [
            'ipDb' => storage_path('Resolver/Ip/ipDb.csv'),
        ]);

        $driverMock = $this->createMock(Driver::class);
        $driverMock->method('supports')->willReturn('random-gibberish');

        try {
            app(GetFileForDriverAction::class)
                ->execute($driverMock);
        } catch (ConfigNotFoundException $e) {
            $this->assertInstanceOf(ConfigNotFoundException::class, $e);
            return;
        }

        $this->fail('Was expecting a config not found error');
    }

    /** @test */
    public function it_throws_a_file_not_found_exception()
    {
        \File::makeDirectory(storage_path('Resolver'));
        \File::makeDirectory(storage_path('Resolver/Ip'));

        \Config::set('ip-resolver.files', [
            'ipDb' => storage_path('Resolver/Ip/randomGibberish.csv'),
        ]);

        $driverMock = $this->createMock(Driver::class);
        $driverMock->method('supports')->willReturn('ipDb');


        try {
            app(GetFileForDriverAction::class)
                ->execute($driverMock);
        } catch (FileNotFoundException $e) {
            $this->assertInstanceOf(FileNotFoundException::class, $e);
            return;
        }

    }

    protected function tearDown(): void
    {
        \File::deleteDirectory(storage_path('Resolver'));
        parent::tearDown();
    }

}