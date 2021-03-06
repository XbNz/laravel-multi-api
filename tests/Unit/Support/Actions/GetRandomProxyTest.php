<?php

declare(strict_types=1);

namespace XbNz\Resolver\Tests\Unit\Support\Actions;

use Illuminate\Support\Facades\Config;
use XbNz\Resolver\Support\Actions\GetRandomProxyAction;
use XbNz\Resolver\Support\Exceptions\ProxyException;

class GetRandomProxyTest extends \XbNz\Resolver\Tests\TestCase
{
    /** @test */
    public function it_throws_a_proxy_exception_for_invalid_proxy_structures()
    {
        Config::set('resolver.use_proxy', true);
        Config::set('resolver.proxies', ['definitely-not-a-valid-proxy-address']);

        try {
            app(GetRandomProxyAction::class)
                ->execute();
        } catch (ProxyException $e) {
            $this->assertInstanceOf(ProxyException::class, $e);
            return;
        }

        $this->fail('It somehow passed with a bad proxy structure');
    }

    /** @test */
    public function it_picks_a_random_valid_proxy_and_returns_it()
    {
        $proxies = [
            'https://username:password@12.34.56.78:4949',
            'http://192.111.222.32:33',
            'https://2.2.2.2:3333',
        ];

        Config::set('resolver.use_proxy', true);
        Config::set('resolver.proxies', $proxies);

        $randomProxy = app(GetRandomProxyAction::class)
            ->execute();

        $this->assertContains($randomProxy, $proxies);
    }

    /** @test **/
    public function if_the_client_has_chosen_to_use_proxies_but_hasnt_provided_any_it_throws_an_exception(): void
    {
        $proxies = [];

        Config::set('resolver.use_proxy', true);
        Config::set('resolver.proxies', $proxies);

        $this->expectException(ProxyException::class);
        $randomProxy = app(GetRandomProxyAction::class)->execute();
    }
}
