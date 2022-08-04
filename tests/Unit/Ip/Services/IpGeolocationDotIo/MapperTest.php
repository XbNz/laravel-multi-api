<?php

declare(strict_types=1);

namespace XbNz\Resolver\Tests\Unit\Ip\Services\IpGeolocationDotIo;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Domain\Ip\Services\IpGeolocationDotIo\IpGeolocationDotIoService;
use XbNz\Resolver\Factories\GuzzleClientFactory;
use XbNz\Resolver\Support\DTOs\RequestResponseWrapper;
use XbNz\Resolver\Tests\TestCase;

class MapperTest extends TestCase
{
    /** @test **/
    public function geolocation_data_is_mapped_correctly(): void
    {
        // Arrange
        $clientFactoryMock = $this->mock(GuzzleClientFactory::class);
        $sampleData = $this->sampleGeolocationData();

        $mockHandler = new MockHandler([
            new Response(200, [
                'Content-Type' => 'application/json',
            ], json_encode($sampleData, JSON_THROW_ON_ERROR)),
        ]);

        $clientFactoryMock->shouldReceive('for')
            ->once()
            ->with(IpGeolocationDotIoService::class)
            ->andReturn(new Client([
                'handler' => $mockHandler,
            ]));

        $service = app(IpGeolocationDotIoService::class);

        // Act
        $ran = 0;
        $collection = $service->geolocate([IpData::fromIp('1.1.1.1')], function (array $raw) use (&$ran): void {
            $this->assertContainsOnlyInstancesOf(RequestResponseWrapper::class, $raw);
            $ran++;
        });

        // Assert
        $this->assertEquals(1, $ran);

        $this->assertSame('1.1.1.1', $collection->first()->ip->ip);
        $this->assertSame('NA', $collection->first()->continent->code);
        $this->assertSame('US', $collection->first()->country->alpha2);
        $this->assertSame('Washington, D.C.', $collection->first()->capital);
        $this->assertSame('California', $collection->first()->stateOrProvince);
        $this->assertSame('Los Angeles', $collection->first()->district);
        $this->assertSame('Los Angeles', $collection->first()->city);
        $this->assertSame('90012', $collection->first()->zipCode);
        $this->assertSame(34.05361, $collection->first()->coordinates->latitude);
        $this->assertSame(-118.24550, $collection->first()->coordinates->longitude);
        $this->assertFalse($collection->first()->isEu);
        $this->assertSame('+1', $collection->first()->callingCode);
        $this->assertSame('.us', $collection->first()->topLevelDomain);
        $this->assertSame(['en-US', 'es-US', 'haw', 'fr'], $collection->first()->languages);
        $this->assertSame('https://ipgeolocation.io/static/flags/us_64.png', $collection->first()->flagImageUrl);
        $this->assertSame('5332870', $collection->first()->geoNameId);
        $this->assertSame('APNIC Research and Development', $collection->first()->isp);
        $this->assertSame('', $collection->first()->connectionType);
        $this->assertSame('Cloudflare, Inc.', $collection->first()->organization);
        $this->assertSame([
            'code' => 'USD',
            'name' => 'US Dollar',
            'symbol' => '$',
        ], $collection->first()->currency);

        $this->assertSame([
            'name' => 'America/Los_Angeles',
            'offset' => -8,
            'current_time' => '2022-08-02 13:58:18.058-0700',
            'current_time_unix' => 1659473898.058,
            'is_dst' => true,
            'dst_savings' => 1,
        ], $collection->first()->timeZone);
    }

    public function sampleGeolocationData(array $extras = []): array
    {
        $json = <<<JSON
        {
            "ip": "1.1.1.1",
            "continent_code": "NA",
            "continent_name": "North America",
            "country_code2": "US",
            "country_code3": "USA",
            "country_name": "United States",
            "country_capital": "Washington, D.C.",
            "state_prov": "California",
            "district": "Los Angeles",
            "city": "Los Angeles",
            "zipcode": "90012",
            "latitude": "34.05361",
            "longitude": "-118.24550",
            "is_eu": false,
            "calling_code": "+1",
            "country_tld": ".us",
            "languages": "en-US,es-US,haw,fr",
            "country_flag": "https://ipgeolocation.io/static/flags/us_64.png",
            "geoname_id": "5332870",
            "isp": "APNIC Research and Development",
            "connection_type": "",
            "organization": "Cloudflare, Inc.",
            "currency":
            {
                "code": "USD",
                "name": "US Dollar",
                "symbol": "$"
            },
            "time_zone":
            {
                "name": "America/Los_Angeles",
                "offset": -8,
                "current_time": "2022-08-02 13:58:18.058-0700",
                "current_time_unix": 1659473898.058,
                "is_dst": true,
                "dst_savings": 1
            }
        }
        JSON;

        $array = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        return array_merge($array, $extras);
    }
}
