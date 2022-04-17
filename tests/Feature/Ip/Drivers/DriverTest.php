<?php

declare(strict_types=1);

namespace XbNz\Resolver\Tests\Feature\Ip\Drivers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use XbNz\Resolver\Domain\Ip\Actions\MtrProbeSearchAction;
use XbNz\Resolver\Domain\Ip\Builders\IpBuilder;
use XbNz\Resolver\Domain\Ip\Collections\IpCollection;
use XbNz\Resolver\Domain\Ip\Drivers\AbuseIpDbDotComDriver;
use XbNz\Resolver\Domain\Ip\Drivers\IpApiDotComDriver;
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
            'resolver.tries' => 3,
            'resolver.retry_sleep' => 2,
            'resolver.timeout' => 50,
            'ip-resolver.XbNz\Resolver\Domain\Ip\Drivers\MtrDotShMtrDriver.search' => 'vienna',
            'ip-resolver.XbNz\Resolver\Domain\Ip\Drivers\MtrDotShPingDriver.search' => 'vienna'
        ]);

        app(Resolver::class)->ip()
            ->abuseIpDbDotCom()
            ->ipApiDotCom()
            ->ipDataDotCo()
            ->ipGeolocationDotIo()
            ->ipInfoDotIo()
            ->mtrDotShMtr()
            ->mtrDotShPing()
            ->withIps(['1.1.1.1'])
            ->normalize();

        $before = now();
        app(Resolver::class)->ip()->withDrivers([
            AbuseIpDbDotComDriver::class,
            IpApiDotComDriver::class,
            IpGeolocationDotIoDriver::class,
            IpInfoDotIoDriver::class,
            MtrDotShMtrDriver::class,
            MtrDotShPingDriver::class,
        ])->withIps(['1.1.1.1'])->normalize();
        $after = now();

        $this->assertLessThanOrEqual(5000, $after->diffInMilliseconds($before));
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
            Config::set(["ip-resolver.api-keys.{$driver}" => ['incorrect-api-key']]);

            try {
                app(Resolver::class)->ip()->withDrivers([
                    $driver
                ])->withIps(['1.1.1.1'])->normalize();
            } catch (ApiProviderException $e) {
                $this->assertTrue(true);
                continue;
            }

            $this->fail('Expected exception not thrown');
        }
    }

}
