<?php

declare(strict_types=1);

namespace XbNz\Resolver\Tests\Feature\Ip\Drivers;

use Illuminate\Support\Facades\Config;
use XbNz\Resolver\Domain\Ip\Drivers\AbuseIpDbDotComDriver;
use XbNz\Resolver\Domain\Ip\Drivers\IpApiDotComDriver;
use XbNz\Resolver\Domain\Ip\Drivers\IpDashApiDotComDriver;
use XbNz\Resolver\Domain\Ip\Drivers\IpDataDotCoDriver;
use XbNz\Resolver\Domain\Ip\Drivers\IpGeolocationDotIoDriver;
use XbNz\Resolver\Domain\Ip\Drivers\IpInfoDotIoDriver;
use XbNz\Resolver\Domain\Ip\Drivers\MtrDotShMtrDriver;
use XbNz\Resolver\Domain\Ip\Drivers\MtrDotShPingDriver;
use XbNz\Resolver\Resolver\Resolver;
use XbNz\Resolver\Support\Exceptions\ApiProviderException;

class DriverTest extends \XbNz\Resolver\Tests\TestCase
{
    /** @test
     * @group Online
     */
    public function it_resolves_ip_information_and_caches_them_for_the_2nd_go()
    {
        Config::set([
            'resolver.async_concurrent_requests' => 10,
            'resolver.cache_period' => 200,
            'resolver.use_retries' => true,
            'resolver.tries' => 10,
            'resolver.retry_sleep' => 10000,
            'resolver.timeout' => 50,
            'ip-resolver.XbNz\Resolver\Domain\Ip\Drivers\MtrDotShMtrDriver.search' => 'vienna',
            'ip-resolver.XbNz\Resolver\Domain\Ip\Drivers\MtrDotShPingDriver.search' => 'vienna',
        ]);

        app(Resolver::class)->ip()
            ->abuseIpDbDotCom()
//            ->ipApiDotCom()
            ->ipDataDotCo()
            ->ipGeolocationDotIo()
            ->ipInfoDotIo()
            ->mtrDotShMtr()
            ->mtrDotShPing()
            ->ipDashApiDotCom()
            ->withIps(['1.1.1.1', '2.2.2.2'])
            ->normalize();

        $before = now();
        app(Resolver::class)->ip()->withDrivers([
            AbuseIpDbDotComDriver::class,
//            IpApiDotComDriver::class,
            IpDataDotCoDriver::class,
            IpGeolocationDotIoDriver::class,
            IpInfoDotIoDriver::class,
            MtrDotShMtrDriver::class,
            MtrDotShPingDriver::class,
            IpDashApiDotComDriver::class
        ])->withIps(['1.1.1.1'])->normalize();
        $after = now();

        $this->assertLessThanOrEqual(2000, $after->diffInMilliseconds($before));
    }

    /** @test
     * @group Online
     */
    public function provided_an_incorrect_api_key_it_throws_the_expected_exception(): void
    {
        $testedDrivers = [
            AbuseIpDbDotComDriver::class,
            IpApiDotComDriver::class,
            IpGeolocationDotIoDriver::class,
        ];

        foreach ($testedDrivers as $driver) {
            Config::set([
                "ip-resolver.api-keys.{$driver}" => ['incorrect-api-key'],
            ]);

            try {
                app(Resolver::class)->ip()->withDrivers([
                    $driver,
                ])->withIps(['1.1.1.1'])->normalize();
            } catch (ApiProviderException $e) {
                $this->assertTrue(true);
                continue;
            }

            $this->fail('Expected exception not thrown');
        }
    }
}
