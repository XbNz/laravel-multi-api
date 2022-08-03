<?php

declare(strict_types=1);

namespace XbNz\Resolver\Domain\Ip\Services\IpGeolocationDotIo\Mappers;

use JsonException;
use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Domain\Ip\Services\IpGeolocationDotIo\DTOs\IpGeolocationResultData;
use XbNz\Resolver\Domain\Ip\Services\IpGeolocationDotIo\Exceptions\IpGeolocationDotIoException;
use XbNz\Resolver\Support\DTOs\RequestResponseWrapper;
use XbNz\Resolver\Support\ValueObjects\Continent;
use XbNz\Resolver\Support\ValueObjects\Coordinates;
use XbNz\Resolver\Support\ValueObjects\Country;

class GeolocationMapper
{
    public function map(RequestResponseWrapper $requestResponse): IpGeolocationResultData
    {
        try {
            $jsonResponse = json_decode(
                $requestResponse->guzzleResponse->getBody()->getContents(),
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (JsonException $e) {
            throw new IpGeolocationDotIoException('Failed to decode JSON response from IPGeolocation');
        }

        return new IpGeolocationResultData(
            IpData::fromIp($jsonResponse['ip']),
            Continent::fromCode($jsonResponse['continent_code']),
            Country::from($jsonResponse['country_code2']),
            $jsonResponse['country_capital'],
            $jsonResponse['state_prov'],
            $jsonResponse['district'],
            $jsonResponse['city'],
            $jsonResponse['zipcode'],
            Coordinates::from(
                (float) $jsonResponse['latitude'],
                (float) $jsonResponse['longitude'],
            ),
            $jsonResponse['is_eu'],
            $jsonResponse['calling_code'],
            $jsonResponse['country_tld'],
            explode(',', $jsonResponse['languages']),
            $jsonResponse['country_flag'],
            $jsonResponse['geoname_id'],
            $jsonResponse['isp'],
            $jsonResponse['connection_type'],
            $jsonResponse['organization'],
            $jsonResponse['currency'],
            $jsonResponse['time_zone'],
        );
    }
}
