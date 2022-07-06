<?php

declare(strict_types=1);

namespace XbNz\Resolver\Tests\Feature\Ip\Drivers;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use XbNz\Resolver\Domain\Ip\Drivers\AbstractApiDotComDriver;
use XbNz\Resolver\Domain\Ip\Drivers\AbuseIpDbDotComDriver;
use XbNz\Resolver\Domain\Ip\Drivers\IpApiDotCoDriver;
use XbNz\Resolver\Domain\Ip\Drivers\IpApiDotComDriver;
use XbNz\Resolver\Domain\Ip\Drivers\IpDashApiDotComDriver;
use XbNz\Resolver\Domain\Ip\Drivers\IpDataDotCoDriver;
use XbNz\Resolver\Domain\Ip\Drivers\IpGeolocationDotIoDriver;
use XbNz\Resolver\Domain\Ip\Drivers\IpInfoDotIoDriver;
use XbNz\Resolver\Domain\Ip\Drivers\MtrDotShMtrDriver;
use XbNz\Resolver\Domain\Ip\Drivers\MtrDotShPingDriver;
use XbNz\Resolver\Resolver\Resolver;
use XbNz\Resolver\Support\DTOs\RequestResponseWrapper;
use XbNz\Resolver\Support\Exceptions\ApiProviderException;

class DriverTest extends \XbNz\Resolver\Tests\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Config::set([
            'resolver.async_concurrent_requests' => 20,
            'resolver.cache_period' => 200,
            'resolver.use_retries' => true,
            'resolver.tries' => 3,
            'resolver.retry_sleep' => 2,
            'resolver.timeout' => 50,
            'ip-resolver.XbNz\Resolver\Domain\Ip\Drivers\MtrDotShMtrDriver.search' => 'vienna',
            'ip-resolver.XbNz\Resolver\Domain\Ip\Drivers\MtrDotShPingDriver.search' => 'vienna',
        ]);
    }

    /** @test
     * @group Online
     */
    public function it_resolves_ip_information_and_caches_them_for_the_2nd_go()
    {
        app(Resolver::class)->ip()
            ->abuseIpDbDotCom()
            ->ipApiDotCom()
            ->ipDataDotCo()
            ->ipGeolocationDotIo()
            ->ipInfoDotIo()
            ->mtrDotShMtr()
            ->mtrDotShPing()
            ->ipDashApiDotCom()
            ->ipApiDotCo()
            ->abstractApiDotCom()
            ->withIps(['1.1.1.1', '2606:4700:4700::1111'])
            ->normalize();

        $before = now();
        app(Resolver::class)->ip()->withDrivers([
            AbuseIpDbDotComDriver::class,
            IpApiDotComDriver::class,
            IpDataDotCoDriver::class,
            IpGeolocationDotIoDriver::class,
            IpInfoDotIoDriver::class,
            MtrDotShMtrDriver::class,
            MtrDotShPingDriver::class,
            IpDashApiDotComDriver::class,
            IpApiDotCoDriver::class,
            AbstractApiDotComDriver::class,
        ])->withIps(['1.1.1.1', '2606:4700:4700::1111'])->normalize();
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
            AbstractApiDotComDriver::class,
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

    /** @test
     * @group Online
     */
    public function required_fields()
    {
        $requiredFields = [
            AbstractApiDotComDriver::class => [
                'ip_address',
                'country',
                'city',
                'latitude',
                'longitude',
                'connection.isp_name',
            ],

            AbuseIpDbDotComDriver::class => [
                'data.ipAddress',
                'data.countryCode',
                'data.isp',
            ],

            IpApiDotCoDriver::class => [
                'ip',
                'country_name',
                'city',
                'latitude',
                'longitude',
                'org',
            ],

            IpApiDotComDriver::class => [
                'ip',
                'country_name',
                'city',
                'latitude',
                'longitude',
            ],

            IpDashApiDotComDriver::class => [
                'query',
                'country',
                'city',
                'lat',
                'lon',
                'as',
            ],

            IpDataDotCoDriver::class => [
                'ip',
                'country_name',
                'city',
                'latitude',
                'longitude',
                'asn.name',
            ],

            IpGeolocationDotIoDriver::class => [
                'ip',
                'country_name',
                'city',
                'latitude',
                'longitude',
                'organization',
            ],

            IpInfoDotIoDriver::class => [
                'ip',
                'country',
                'city',
                'loc',
                'org',
            ],
        ];

        foreach ($requiredFields as $driver => $fields) {
            $raw = app(Resolver::class)->ip()->withDrivers([
                $driver,
            ])->withIps(['1.1.1.1'])->raw();

            $this->assertInstanceOf(RequestResponseWrapper::class, $raw[0]);
            $dotNotatedResultSet = Arr::dot($raw[0]->data);

            foreach ($fields as $field) {
                $this->assertArrayHasKey($field, $dotNotatedResultSet);
            }
        }
    }
}
