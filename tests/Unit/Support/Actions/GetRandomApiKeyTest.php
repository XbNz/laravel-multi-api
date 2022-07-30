<?php

declare(strict_types=1);

namespace XbNz\Resolver\Tests\Unit\Support\Actions;

use function app;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use XbNz\Resolver\Support\Actions\GetRandomApiKeyAction;
use XbNz\Resolver\Support\Exceptions\ConfigNotFoundException;
use XbNz\Resolver\Support\Exceptions\MissingApiKeyException;

class GetRandomApiKeyTest extends \XbNz\Resolver\Tests\TestCase
{
    /** @test */
    public function it_fetches_the_key_for_the_given_service()
    {
        Config::set('ip-resolver.api-keys', [
            'SomeRandomService::class' => ['::api-key-1::', '::api-key-2::'],

            '::just random noise::' => ['::these shouldnt::', '::even see::', '::the light of::', '::day::'],
            '::just random noisee::' => ['::these shouldnt::', '::even see::', '::the light of::', '::day::'],
            '::just random noiseee::' => ['::these shouldnt::', '::even see::', '::the light of::', '::day::'],
            '::just random noiseeee::' => ['::these shouldnt::', '::even see::', '::the light of::', '::day::'],
        ]);

        $service = 'SomeRandomService::class';

        $key = Str::of(app(GetRandomApiKeyAction::class)->execute($service, 'ip-resolver.api-keys'));

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
    public function if_the_api_service_is_in_the_config_but_no_key_is_provided_it_throws_an_exception(): void
    {
        Config::set('ip-resolver.api-keys', [
            'ServiceIsPresentButNoApiKeyAssigned::class' => [],
        ]);

        $driver = 'ServiceIsPresentButNoApiKeyAssigned::class';

        try {
            app(GetRandomApiKeyAction::class)->execute($driver, 'ip-resolver.api-keys');
        } catch (MissingApiKeyException $e) {
            $this->assertInstanceOf(MissingApiKeyException::class, $e);
            return;
        }

        $this->fail('Did not throw config error');
    }
}
