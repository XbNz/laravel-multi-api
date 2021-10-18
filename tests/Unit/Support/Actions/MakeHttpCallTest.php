<?php

namespace XbNz\Resolver\Tests\Unit\Support\Actions;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use XbNz\Resolver\Support\Actions\MakeHttpCallAction;

class MakeHttpCallTest extends \XbNz\Resolver\Tests\TestCase
{
    /** @test */
    public function it_successfully_makes_a_call()
    {
        \Config::set('resolver.use_proxy', false);
        $result = app(MakeHttpCallAction::class)
            ->execute('http://ip-api.com/json/');
        $this->assertEquals('success', $result->json()['status']);
    }

    /** @test */
    public function it_applies_the_config_timeout()
    {
        \Config::set('resolver.use_proxy', true);
        \Config::set('resolver.timeout', 0.01);
        \Config::set('resolver.proxies', ['https://1.1.1.1:8080']);

        try {
            app(MakeHttpCallAction::class)
                ->execute('http://ip-api.com/json/');
        } catch (ConnectionException $e){
            $this->assertInstanceOf(ConnectionException::class, $e);
            return;
        }
        $this->fail('Timeout not respected.');
    }
}