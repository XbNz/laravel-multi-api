<?php

declare(strict_types=1);

namespace XbNz\Resolver\Factories;

use JsonException;
use Psr\Http\Message\ResponseInterface;
use XbNz\Resolver\Domain\Ip\Drivers\AbstractApiDotComDriver;
use XbNz\Resolver\Domain\Ip\Drivers\AbuseIpDbDotComDriver;
use XbNz\Resolver\Domain\Ip\Drivers\IpApiDotCoDriver;
use XbNz\Resolver\Domain\Ip\Drivers\IpApiDotComDriver;
use XbNz\Resolver\Domain\Ip\Drivers\IpDashApiDotComDriver;
use XbNz\Resolver\Domain\Ip\Drivers\IpGeolocationDotIoDriver;
use XbNz\Resolver\Domain\Ip\Drivers\IpInfoDotIoDriver;
use XbNz\Resolver\Domain\Ip\Drivers\MtrDotShMtrDriver;
use XbNz\Resolver\Domain\Ip\Drivers\MtrDotShPingDriver;
use XbNz\Resolver\Support\DTOs\RequestResponseWrapper;

class RawResultsFactory
{
    /**
     * @throws JsonException
     */
    public static function fromResponse(ResponseInterface $response, string $request): RequestResponseWrapper
    {
        return new RequestResponseWrapper(
            $request,
            json_decode((string) $response->getBody(), true, flags: JSON_THROW_ON_ERROR)
        );
    }

//    /**
//     * @param array<mixed> $overrides
//     */
//    public static function abuseIpDbDotComFake(array $overrides = []): RawResultsData
//    {
//        return new RawResultsData(
//            AbuseIpDbDotComDriver::class,
//            array_merge([
//                'data' => [
//                    'ipAddress' => '1.1.1.1',
//                    'isPublic' => true,
//                    'ipVersion' => 4,
//                    'isWhitelisted' => true,
//                    'abuseConfidenceScore' => 0,
//                    'countryCode' => 'US',
//                    'usageType' => 'Content Delivery Network',
//                    'isp' => 'APNIC and CloudFlare DNS Resolver Project',
//                    'domain' => 'cloudflare.com',
//                    'hostnames' => [
//                        0 => 'one.one.one.one',
//                    ],
//                    'totalReports' => 152,
//                    'numDistinctUsers' => 69,
//                    'lastReportedAt' => '2022-04-13T07:50:50+00:00',
//                ],
//            ], $overrides)
//        );
//    }
//
//    /**
//     * @param array<mixed> $overrides
//     */
//    public static function ipApiDotComFake(array $overrides = []): RawResultsData
//    {
//        return new RawResultsData(
//            IpApiDotComDriver::class,
//            array_merge([
//                'ip' => '1.1.1.1',
//                'type' => 'ipv4',
//                'continent_code' => 'OC',
//                'continent_name' => 'Oceania',
//                'country_code' => 'AU',
//                'country_name' => 'Australia',
//                'region_code' => 'VIC',
//                'region_name' => 'Victoria',
//                'city' => 'Balwyn North',
//                'zip' => '3095',
//                'latitude' => -37.703601837158,
//                'longitude' => 145.18063354492,
//                'location' => [
//                    'geoname_id' => 7932629,
//                    'capital' => 'Canberra',
//                    'languages' => [
//                        0 => [
//                            'code' => 'en',
//                            'name' => 'English',
//                            'native' => 'English',
//                        ],
//                    ],
//                    'country_flag' => 'https://assets.ipstack.com/flags/au.svg',
//                    'country_flag_emoji' => '🇦🇺',
//                    'country_flag_emoji_unicode' => 'U+1F1E6 U+1F1FA',
//                    'calling_code' => '61',
//                    'is_eu' => false,
//                ],
//            ], $overrides)
//        );
//    }
//
//    /**
//     * @param array<mixed> $overrides
//     */
//    public static function ipDataDotCoFake(array $overrides = []): RawResultsData
//    {
//        return new RawResultsData(
//            IpDashApiDotComDriver::class,
//            array_merge([
//                'ip' => '1.1.1.1',
//                'is_eu' => false,
//                'city' => null,
//                'region' => null,
//                'region_code' => null,
//                'country_name' => 'Australia',
//                'country_code' => 'AU',
//                'continent_name' => 'Oceania',
//                'continent_code' => 'OC',
//                'latitude' => -33.494,
//                'longitude' => 143.2104,
//                'postal' => null,
//                'calling_code' => '61',
//                'flag' => 'https://ipdata.co/flags/au.png',
//                'emoji_flag' => '🇦🇺',
//                'emoji_unicode' => 'U+1F1E6 U+1F1FA',
//                'asn' => [
//                    'asn' => 'AS13335',
//                    'name' => 'Cloudflare, Inc.',
//                    'domain' => 'cloudflare.com',
//                    'route' => '1.1.1.0/24',
//                    'type' => 'business',
//                ],
//                'company' => [
//                    'name' => 'Asia Pacific Network Information Centre ',
//                    'domain' => 'apnic.net',
//                    'type' => 'business',
//                ],
//                'languages' => [
//                    0 => [
//                        'name' => 'English',
//                        'native' => 'English',
//                        'code' => 'en',
//                    ],
//                ],
//                'currency' => [
//                    'name' => 'Australian Dollar',
//                    'code' => 'AUD',
//                    'symbol' => 'AU$',
//                    'native' => '$',
//                    'plural' => 'Australian dollars',
//                ],
//                'time_zone' => [
//                    'name' => 'Australia/Sydney',
//                    'abbr' => 'AEST',
//                    'offset' => '+1000',
//                    'is_dst' => false,
//                    'current_time' => '2022-04-18T12:41:26+10:00',
//                ],
//                'threat' => [
//                    'is_tor' => false,
//                    'is_icloud_relay' => false,
//                    'is_proxy' => true,
//                    'is_datacenter' => false,
//                    'is_anonymous' => true,
//                    'is_known_attacker' => false,
//                    'is_known_abuser' => false,
//                    'is_threat' => false,
//                    'is_bogon' => false,
//                    'blocklists' => [],
//                    'additional_info' => [
//                        0 => 'https://spur.us/context/1.1.1.1',
//                    ],
//                ],
//                'count' => '7',
//            ], $overrides)
//        );
//    }
//
//    /**
//     * @param array<mixed> $overrides
//     */
//    public static function ipGeolocationDotIoFake(array $overrides = []): RawResultsData
//    {
//        return new RawResultsData(
//            IpGeolocationDotIoDriver::class,
//            array_merge([
//                'ip' => '1.1.1.1',
//                'continent_code' => 'NA',
//                'continent_name' => 'North America',
//                'country_code2' => 'US',
//                'country_code3' => 'USA',
//                'country_name' => 'United States',
//                'country_capital' => 'Washington, D.C.',
//                'state_prov' => 'California',
//                'district' => 'Los Angeles',
//                'city' => 'Los Angeles',
//                'zipcode' => '90012',
//                'latitude' => '34.05361',
//                'longitude' => '-118.24550',
//                'is_eu' => false,
//                'calling_code' => '+1',
//                'country_tld' => '.us',
//                'languages' => 'en-US,es-US,haw,fr',
//                'country_flag' => 'https://ipgeolocation.io/static/flags/us_64.png',
//                'geoname_id' => '5332870',
//                'isp' => 'APNIC Research and Development',
//                'connection_type' => '',
//                'organization' => 'Cloudflare, Inc.',
//                'currency' => [
//                    'code' => 'USD',
//                    'name' => 'US Dollar',
//                    'symbol' => '$',
//                ],
//                'time_zone' => [
//                    'name' => 'America/Los_Angeles',
//                    'offset' => -8,
//                    'current_time' => '2022-04-18 05:50:04.636-0700',
//                    'current_time_unix' => 1650286204.636,
//                    'is_dst' => true,
//                    'dst_savings' => 1,
//                ],
//            ], $overrides)
//        );
//    }
//
//    /**
//     * @param array<mixed> $overrides
//     */
//    public static function ipInfoDotIoFake(array $overrides = []): RawResultsData
//    {
//        return new RawResultsData(
//            IpInfoDotIoDriver::class,
//            array_merge([
//                'ip' => '1.1.1.1',
//                'hostname' => 'one.one.one.one',
//                'anycast' => true,
//                'city' => 'Los Angeles',
//                'region' => 'California',
//                'country' => 'US',
//                'loc' => '34.0522,-118.2437',
//                'org' => 'AS13335 Cloudflare, Inc.',
//                'postal' => '90076',
//                'timezone' => 'America/Los_Angeles',
//            ], $overrides)
//        );
//    }
//
//    /**
//     * @param array<mixed> $overrides
//     */
//    public static function mtrDotShMtrFake(array $overrides = []): RawResultsData
//    {
//        return new RawResultsData(
//            MtrDotShMtrDriver::class,
//            array_merge([
//                'probe_id' => '7xWtI',
//                'target_ip' => '1.1.1.1',
//                'hops' => [
//                    1 => [
//                        'hop_host' => '94.16.98.160',
//                        'statistics' => [
//                            'Loss%' => '0.0%',
//                            'Drop' => '0',
//                            'Rcv' => '10',
//                            'Snt' => '10',
//                            'Last' => '0.5',
//                            'Best' => '0.2',
//                            'Avg' => '5.3',
//                            'Wrst' => '41.0',
//                            'StDev' => '12.9',
//                            'Gmean' => '0.7',
//                            'Jttr' => '8.7',
//                            'Javg' => '9.9',
//                            'Jmax' => '40.8',
//                            'Jint' => '70.8',
//                        ],
//                    ],
//                ],
//            ], $overrides)
//        );
//    }
//
//    /**
//     * @param array<mixed> $overrides
//     */
//    public static function mtrDotShPingFake(array $overrides = []): RawResultsData
//    {
//        return new RawResultsData(
//            MtrDotShPingDriver::class,
//            array_merge([
//                'probe_id' => '7xWtI',
//                'target_ip' => '1.1.1.1',
//                'sequences' => [
//                    0 => [
//                        'size' => 64,
//                        'ip' => '1.1.1.1',
//                        'sequence_number' => 1,
//                        'time_to_live' => 55,
//                        'rtt' => 13,
//                    ],
//                ],
//                'packet_loss' => 0,
//                'statistics' => [
//                    'minimum_rtt' => 12.1,
//                    'average_rtt' => 16.71,
//                    'maximum_rtt' => 54.9,
//                    'jitter' => 9.7,
//                ],
//            ], $overrides)
//        );
//    }
//
//    /**
//     * @param array<mixed> $overrides
//     */
//    public static function ipDashApiDotComFake(array $overrides = []): RawResultsData
//    {
//        return new RawResultsData(
//            IpDashApiDotComDriver::class,
//            array_merge(json_decode('{
//                "status": "success",
//                "continent": "Oceania",
//                "continentCode": "OC",
//                "country": "Australia",
//                "countryCode": "AU",
//                "region": "QLD",
//                "regionName": "Queensland",
//                "city": "South Brisbane",
//                "district": "",
//                "zip": "4101",
//                "lat": -27.4766,
//                "lon": 153.0166,
//                "timezone": "Australia/Brisbane",
//                "offset": 36000,
//                "currency": "AUD",
//                "isp": "Cloudflare, Inc",
//                "org": "APNIC and Cloudflare DNS Resolver project",
//                "as": "AS13335 Cloudflare, Inc.",
//                "asname": "CLOUDFLARENET",
//                "reverse": "one.one.one.one",
//                "mobile": false,
//                "proxy": false,
//                "hosting": true,
//                "query": "1.1.1.1"
//            }', true, 512, JSON_THROW_ON_ERROR), $overrides)
//        );
//    }
//
//    /**
//     * @param array<mixed> $overrides
//     */
//    public static function ipApiDotCoFake(array $overrides = []): RawResultsData
//    {
//        return new RawResultsData(
//            IpApiDotCoDriver::class,
//            array_merge(json_decode('{
//                "ip": "8.8.8.8",
//                "version": "IPv4",
//                "city": "Mountain View",
//                "region": "California",
//                "region_code": "CA",
//                "country": "US",
//                "country_name": "United States",
//                "country_code": "US",
//                "country_code_iso3": "USA",
//                "country_capital": "Washington",
//                "country_tld": ".us",
//                "continent_code": "NA",
//                "in_eu": false,
//                "postal": "94043",
//                "latitude": 37.42301,
//                "longitude": -122.083352,
//                "timezone": "America/Los_Angeles",
//                "utc_offset": "-0700",
//                "country_calling_code": "+1",
//                "currency": "USD",
//                "currency_name": "Dollar",
//                "languages": "en-US,es-US,haw,fr",
//                "country_area": 9629091.0,
//                "country_population": 327167434,
//                "asn": "AS15169",
//                "org": "GOOGLE"
//            }', true, 512, JSON_THROW_ON_ERROR), $overrides)
//        );
//    }
//
//    /**
//     * @param array<mixed> $overrides
//     */
//    public static function abstractApiDotComFake(array $overrides = []): RawResultsData
//    {
//        return new RawResultsData(
//            AbstractApiDotComDriver::class,
//            array_merge(json_decode('{
//                "ip_address": "166.171.248.255",
//                "city": "Modesto",
//                "city_geoname_id": 5373900,
//                "region": "California",
//                "region_iso_code": "CA",
//                "region_geoname_id": 5332921,
//                "postal_code": "95353",
//                "country": "United States",
//                "country_code": "US",
//                "country_geoname_id": 6252001,
//                "country_is_eu": false,
//                "continent": "North America",
//                "continent_code": "NA",
//                "continent_geoname_id": 6255149,
//                "longitude": -120.997,
//                "latitude": 37.6393,
//                "security":
//                {
//                    "is_vpn": false
//                },
//                "timezone":
//                {
//                    "name": "America/Los_Angeles",
//                    "abbreviation": "PST",
//                    "gmt_offset": -8,
//                    "current_time": "07:10:37",
//                    "is_dst": false
//                },
//                "flag":
//                {
//                    "emoji": "🇺🇸",
//                    "unicode": "U+1F1FA U+1F1F8",
//                    "png": "https://static.abstractapi.com/country-flags/US_flag.png",
//                    "svg": "https://static.abstractapi.com/country-flags/US_flag.svg"
//                },
//                "currency":
//                {
//                    "currency_name": "USD",
//                    "currency_code": "USD"
//                },
//                "connection":
//                {
//                    "autonomous_system_number": 20057,
//                    "autonomous_system_organization": "AT&T Mobility LLC",
//                    "connection_type": "Cellular",
//                    "isp_name": "AT&T Mobility LLC",
//                    "organization_name": "Service Provider Corporation"
//                }
//            }', true, 512, JSON_THROW_ON_ERROR), $overrides)
//        );
//    }
}
