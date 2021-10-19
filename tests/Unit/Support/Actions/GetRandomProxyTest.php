<?php

namespace XbNz\Resolver\Tests\Unit\Support\Actions;

use XbNz\Resolver\Support\Actions\GetRandomProxyAction;
use XbNz\Resolver\Support\Exceptions\ProxyNotValidException;

class GetRandomProxyTest extends \XbNz\Resolver\Tests\TestCase
{
    /** @test */
    public function it_throws_a_proxy_exception_for_invalid_proxy_structures()
    {
        \Config::set('resolver.use_proxy', true);
        \Config::set('resolver.proxies', ['definitely-not-a-valid-proxy-address']);

        try {
            app(GetRandomProxyAction::class)
                ->execute();
        } catch (ProxyNotValidException $e){
            $this->assertInstanceOf(ProxyNotValidException::class, $e);
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
            'https://2.2.2.2:3333'
        ];

        \Config::set('resolver.use_proxy', true);
        \Config::set('resolver.proxies', $proxies);

        $randomProxy = app(GetRandomProxyAction::class)
            ->execute();

        $this->assertContains($randomProxy, $proxies);
    }
}