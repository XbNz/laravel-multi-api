<?php
//
//declare(strict_types=1);
//
//namespace XbNz\Resolver\Tests\Feature\Ip\Drivers;
//
//use Illuminate\Support\Facades\Cache;
//use Illuminate\Support\Facades\Config;
//use XbNz\Resolver\Domain\Ip\Collections\IpCollection;
//use XbNz\Resolver\Domain\Ip\Drivers\IpGeolocationDotIoDriver;
//use XbNz\Resolver\Resolver\Resolver;
//use XbNz\Resolver\Support\Exceptions\ApiProviderException;
//
//class IpGeolocationDotIoDriverTest extends \XbNz\Resolver\Tests\TestCase
//{
//    /** @test
//     * @group Online
//     */
//    public function it_resolves_and_caches_the_ip_information()
//    {
//        Config::set('resolver.cache_period', 3600);
//        $info = app(Resolver::class)
//            ->ip()
//            ->ipGeolocationDotIo()
//            ->withIp('1.1.1.1')
//            ->normalize();
//
//        $this->assertInstanceOf(IpCollection::class, $info);
//        $this->assertTrue(\Cache::has(IpGeolocationDotIoDriver::class . '1.1.1.1'));
//    }
//
//    /** @test
//     * @group Online
//     */
//    public function provided_an_incorrect_api_key_it_throws_the_expected_exception(): void
//    {
//        Cache::flush();
//        Config::set('ip-resolver.api-keys.ipGeolocationDotIo', ['wrong-api-key-should-be-refused']);
//
//        $this->expectException(ApiProviderException::class);
//
//        app(Resolver::class)
//            ->ip()
//            ->ipGeolocationDotIo()
//            ->withIp('1.1.1.1')
//            ->normalize();
//    }
//}
