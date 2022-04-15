<?php

namespace XbNz\Resolver\Tests\Unit\Support\Actions;

use GuzzleHttp\Psr7\Uri;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use XbNz\Resolver\Support\Actions\GetRandomApiKeyAction;
use XbNz\Resolver\Support\Exceptions\ConfigNotFoundException;
use XbNz\Resolver\Support\Exceptions\MissingApiKeyException;
use function app;

class GetRandomApiKeyTest extends \XbNz\Resolver\Tests\TestCase
{
    /** @test */
    public function it_fetches_the_key_for_the_given_uri()
    {
        Config::set('ip-resolver.api-keys', [
            'SomeRandomDriver::class' => ['::api-key-1::', '::api-key-2::'],

            '::just random noise::' => ['::these shouldnt::', '::even see::', '::the light of::', '::day::'],
            '::just random noisee::' => ['::these shouldnt::', '::even see::', '::the light of::', '::day::'],
            '::just random noiseee::' => ['::these shouldnt::', '::even see::', '::the light of::', '::day::'],
            '::just random noiseeee::' => ['::these shouldnt::', '::even see::', '::the light of::', '::day::'],
        ]);

        $driver = 'SomeRandomDriver::class';

        $key = Str::of(app(GetRandomApiKeyAction::class)->execute($driver, 'ip-resolver.api-keys'));

        $this->assertTrue(
            $key->contains('::api-key-1::') || $key->contains('::api-key-2::')
        );

    }

    /** @test */
    public function it_throws_an_exception_if_config_key_is_not_set()
    {
        Config::set('ip-resolver.api-keys', null);

        $driver = 'SomeRandomDriver::class';

        try {
            app(GetRandomApiKeyAction::class)->execute($driver, 'ip-resolver.api-keys');
        } catch (ConfigNotFoundException $e) {
            $this->assertInstanceOf(ConfigNotFoundException::class, $e);
            return;
        }

        $this->fail('Did not throw config error');
    }

    /** @test **/
    public function if_the_api_host_is_in_the_config_but_no_key_is_provided_it_throws_an_exception(): void
    {
        Config::set('ip-resolver.api-keys', [
            'DriverIsPresentButNoApiKeyAssigned::class' => [],
        ]);

        $driver = 'DriverIsPresentButNoApiKeyAssigned::class';

        try {
            app(GetRandomApiKeyAction::class)->execute($driver, 'ip-resolver.api-keys');
        } catch (MissingApiKeyException $e) {
            $this->assertInstanceOf(MissingApiKeyException::class, $e);
            return;
        }

        $this->fail('Did not throw config error');
    }
}