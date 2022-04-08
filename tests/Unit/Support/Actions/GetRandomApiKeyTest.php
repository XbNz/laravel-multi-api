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
            'this-needs-to-be-picked-up-by-the-action.org' => ['::api-key-1::', '::api-key-2::'],

            '::just random noise::' => ['::these shouldnt::', '::even see::', '::the light of::', '::day::'],
            '::just random noisee::' => ['::these shouldnt::', '::even see::', '::the light of::', '::day::'],
            '::just random noiseee::' => ['::these shouldnt::', '::even see::', '::the light of::', '::day::'],
            '::just random noiseeee::' => ['::these shouldnt::', '::even see::', '::the light of::', '::day::'],
        ]);

        $provider = 'https://this-needs-to-be-picked-up-by-the-action.org';

        $key = Str::of(app(GetRandomApiKeyAction::class)->execute($provider, 'ip-resolver.api-keys'));

        $this->assertTrue(
            $key->contains('::api-key-1::') || $key->contains('::api-key-2::')
        );
    }

    /** @test */
    public function it_throws_an_exception_if_config_key_is_not_set()
    {
        Config::set('ip-resolver.api-keys', null);

        $provider = 'doesnt-exist-in-null-config-and-should-fail.com.au';

        try {
            app(GetRandomApiKeyAction::class)->execute($provider, 'ip-resolver.api-keys');
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
            'any-name-is-set-but-no-api-key-is-set.biz' => [],
        ]);

        $provider = 'any-name-is-set-but-no-api-key-is-set.biz';

        try {
            app(GetRandomApiKeyAction::class)->execute($provider, 'ip-resolver.api-keys');
        } catch (MissingApiKeyException $e) {
            $this->assertInstanceOf(MissingApiKeyException::class, $e);
            return;
        }

        $this->fail('Did not throw config error');
    }
}