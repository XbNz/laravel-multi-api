<?php

declare(strict_types=1);

namespace XbNz\Resolver\Tests\Unit\Ip\Services\AbstractApiDotCom;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Domain\Ip\Services\AbstractApiDotCom\AbstractApiDotComService;
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
            ->with(AbstractApiDotComService::class)
            ->andReturn(new Client([
                'handler' => $mockHandler,
            ]));

        $service = app(AbstractApiDotComService::class);

        // Act
        $ran = 0;
        $collection = $service->geolocate([IpData::fromIp('92.184.105.98')], function (array $raw) use (&$ran): void {
            $this->assertContainsOnlyInstancesOf(RequestResponseWrapper::class, $raw);
            $ran++;
        });

        // Assert
        $this->assertEquals(1, $ran);

        $this->assertSame('92.184.105.98', $collection->first()->ip->ip);
        $this->assertSame('Paris', $collection->first()->city);
        $this->assertSame(999999, $collection->first()->cityGeoNameId);
        $this->assertSame('Paris', $collection->first()->region);
        $this->assertSame('par', $collection->first()->regionIsoCode);
        $this->assertSame(122222, $collection->first()->regionGeoNameId);
        $this->assertSame('SJWIF8', $collection->first()->postalCode);
        $this->assertSame('FR', $collection->first()->country->alpha2);
        $this->assertSame(3017382, $collection->first()->countryGeoNameId);
        $this->assertTrue($collection->first()->isEu);
        $this->assertSame('EU', $collection->first()->continent->code);
        $this->assertSame(6255148, $collection->first()->continentGeoNameId);
        $this->assertSame(48.8582, $collection->first()->coordinates->latitude);
        $this->assertSame(2.3387, $collection->first()->coordinates->longitude);
        $this->assertSame([
            'is_vpn' => false,
        ], $collection->first()->security);
        $this->assertSame([
            'name' => 'Europe/Paris',
            'abbreviation' => 'CEST',
            'gmt_offset' => 2,
            'current_time' => '23:29:52',
            'is_dst' => true,
        ], $collection->first()->timeZone);
        $this->assertSame([
            'emoji' => '🇫🇷',
            'unicode' => 'U+1F1EB U+1F1F7',
            'png' => 'https://static.abstractapi.com/country-flags/FR_flag.png',
            'svg' => 'https://static.abstractapi.com/country-flags/FR_flag.svg',
        ], $collection->first()->flag);
        $this->assertSame([
            'currency_name' => 'Euros',
            'currency_code' => 'EUR',
        ], $collection->first()->currency);
        $this->assertSame([
            'autonomous_system_number' => 3215,
            'autonomous_system_organization' => 'Orange',
            'connection_type' => 'Cellular',
            'isp_name' => 'Orange S.A.',
            'organization_name' => 'Internet OM',
        ], $collection->first()->connection);
    }

    public function sampleGeolocationData(array $extras = []): array
    {
        $json = <<<JSON
        {
            "ip_address": "92.184.105.98",
            "city": "Paris",
            "city_geoname_id": 999999,
            "region": "Paris",
            "region_iso_code": "par",
            "region_geoname_id": 122222,
            "postal_code": "SJWIF8",
            "country": "France",
            "country_code": "FR",
            "country_geoname_id": 3017382,
            "country_is_eu": true,
            "continent": "Europe",
            "continent_code": "EU",
            "continent_geoname_id": 6255148,
            "longitude": 2.3387,
            "latitude": 48.8582,
            "security":
            {
                "is_vpn": false
            },
            "timezone":
            {
                "name": "Europe/Paris",
                "abbreviation": "CEST",
                "gmt_offset": 2,
                "current_time": "23:29:52",
                "is_dst": true
            },
            "flag":
            {
                "emoji": "🇫🇷",
                "unicode": "U+1F1EB U+1F1F7",
                "png": "https://static.abstractapi.com/country-flags/FR_flag.png",
                "svg": "https://static.abstractapi.com/country-flags/FR_flag.svg"
            },
            "currency":
            {
                "currency_name": "Euros",
                "currency_code": "EUR"
            },
            "connection":
            {
                "autonomous_system_number": 3215,
                "autonomous_system_organization": "Orange",
                "connection_type": "Cellular",
                "isp_name": "Orange S.A.",
                "organization_name": "Internet OM"
            }
        }
        JSON;

        $array = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        return array_merge($array, $extras);
    }
}
