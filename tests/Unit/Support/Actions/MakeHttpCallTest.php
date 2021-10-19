<?php

namespace XbNz\Resolver\Tests\Unit\Support\Actions;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use XbNz\Resolver\Support\Actions\MakeHttpCallAction;
use XbNz\Resolver\Support\Drivers\Driver;
use XbNz\Resolver\Support\Exceptions\ApiProviderException;

class MakeHttpCallTest extends \XbNz\Resolver\Tests\TestCase
{
    //TODO: Either mock http calls or tag test under online group

    private Driver $driver;

    protected function setUp(): void
    {
        $this->driver = $this->createMock(Driver::class);
        parent::setUp();
    }

    /** @test
     * @group Online
     */
    public function it_successfully_makes_a_call()
    {
        \Config::set('resolver.use_proxy', false);
        $result = app(MakeHttpCallAction::class)
            ->execute('http://ip-api.com/json/', $this->driver);
        $this->assertEquals('success', $result->json()['status']);
    }

    /** @test */
    public function it_times_out_and_throws_exception()
    {
        \Config::set('resolver.use_proxy', true);
        \Config::set('resolver.timeout', 0.01);
        \Config::set('resolver.proxies', ['https://1.1.1.1:8080']);

        try {
            app(MakeHttpCallAction::class)
                ->execute('http://ip-api.com/json/', $this->driver);
        } catch (ConnectionException $e){
            $this->assertInstanceOf(ConnectionException::class, $e);
            return;
        }
        $this->fail('Timeout not respected.');
    }

    /** @test */
    public function it_throws_api_exception_if_response_unsuccessful()
    {
        \Config::set('resolver.use_proxy', false);
        \Config::set('resolver.timeout', 10);
        $this->driver->method('supports')->willReturn('testValue');

        Http::fake([
            '*' => Http::response([], 404)
        ]);

        try {
            app(MakeHttpCallAction::class)
                ->execute('https://random.com', $this->driver);
        } catch (ApiProviderException $e){
            $this->assertStringContainsString('testValue', $e->getMessage());
            $this->assertStringContainsString(404, $e->getMessage());
            $this->assertInstanceOf(ApiProviderException::class, $e);
            return;
        }
        $this->fail('Api provider exception expected, none returned.');

    }

}