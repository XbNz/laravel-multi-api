<?php

declare(strict_types=1);

namespace XbNz\Resolver\Domain\Ip\Services\IpGeolocationDotIo\DTOs;

use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Support\ValueObjects\Continent;
use XbNz\Resolver\Support\ValueObjects\Coordinates;
use XbNz\Resolver\Support\ValueObjects\Country;

class IpGeolocationResultData
{
    /**
     * @param array<int, string> $languages
     * @param array{'code': string, 'name': string, 'symbol': string} $currency
     * @param array{'name': string, 'offset': float, 'current_time': string, 'current_time_unix': float, 'is_dst': bool, 'dst_savings': float} $timeZone
     */
    public function __construct(
        public readonly IpData $ip,
        public readonly Continent $continent,
        public readonly Country $country,
        public readonly string $capital,
        public readonly string $stateOrProvince,
        public readonly string $district,
        public readonly string $city,
        public readonly string $zipCode,
        public readonly Coordinates $coordinates,
        public readonly bool $isEu,
        public readonly string $callingCode,
        public readonly string $topLevelDomain,
        public readonly array $languages,
        public readonly string $flagImageUrl,
        public readonly string $geoNameId,
        public readonly string $isp,
        public readonly string $connectionType,
        public readonly string $organization,
        public readonly array $currency,
        public readonly array $timeZone,
    ) {
    }
}
