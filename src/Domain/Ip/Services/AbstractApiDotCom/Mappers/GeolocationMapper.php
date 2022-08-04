<?php

declare(strict_types=1);

namespace XbNz\Resolver\Domain\Ip\Services\AbstractApiDotCom\Mappers;

use JsonException;
use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Domain\Ip\Services\AbstractApiDotCom\DTOs\AbstractApiGeolocationResultsData;
use XbNz\Resolver\Domain\Ip\Services\AbstractApiDotCom\Exceptions\AbstractApiException;
use XbNz\Resolver\Support\DTOs\RequestResponseWrapper;
use XbNz\Resolver\Support\ValueObjects\Continent;
use XbNz\Resolver\Support\ValueObjects\Coordinates;
use XbNz\Resolver\Support\ValueObjects\Country;

class GeolocationMapper
{
    public function map(RequestResponseWrapper $requestResponse): AbstractApiGeolocationResultsData
    {
        try {
            $jsonResponse = json_decode(
                $requestResponse->guzzleResponse->getBody()->getContents(),
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (JsonException $e) {
            throw new AbstractApiException('Failed to decode JSON response from IPGeolocation');
        }

        return new AbstractApiGeolocationResultsData(
            IpData::fromIp($jsonResponse['ip_address']),
            $jsonResponse['city'] ?? null,
            $jsonResponse['city_geoname_id'] ?? null,
            $jsonResponse['region'] ?? null,
            $jsonResponse['region_iso_code'] ?? null,
            $jsonResponse['region_geoname_id'] ?? null,
            $jsonResponse['postal_code'] ?? null,
            Country::from($jsonResponse['country_code']),
            $jsonResponse['country_geoname_id'],
            $jsonResponse['country_is_eu'],
            Continent::fromCode($jsonResponse['continent_code']),
            $jsonResponse['continent_geoname_id'],
            Coordinates::from(
                (float) $jsonResponse['latitude'],
                (float) $jsonResponse['longitude'],
            ),
            $jsonResponse['security'],
            $jsonResponse['timezone'],
            $jsonResponse['flag'],
            $jsonResponse['currency'],
            $jsonResponse['connection']
        );
    }
}
