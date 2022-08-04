<?php

declare(strict_types=1);

namespace XbNz\Resolver\Domain\Ip\Services\AbstractApiDotCom\DTOs;

use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Support\ValueObjects\Continent;
use XbNz\Resolver\Support\ValueObjects\Coordinates;
use XbNz\Resolver\Support\ValueObjects\Country;

class AbstractApiGeolocationResultsData
{
    /**
     * @param array{'is_vpn': bool} $security
     * @param array{'name': string, 'abbreviation': string, 'gmt_offset': float, 'current_time': string, 'is_dst': bool} $timeZone
     * @param array{'emoji': string, 'unicode': string, 'png': string, 'svg': string} $flag
     * @param array{'currency_name': string, 'currency_code': string} $currency
     * @param array{'autonomous_system_number': int, 'autonomous_system_organization': string, 'connection_type': string, 'isp_name': string, 'organization_name': string} $connection
     */
    public function __construct(
        public readonly IpData $ip,
        public readonly ?string $city,
        public readonly ?int $cityGeoNameId,
        public readonly ?string $region,
        public readonly ?string $regionIsoCode,
        public readonly ?int $regionGeoNameId,
        public readonly ?string $postalCode,
        public readonly Country $country,
        public readonly int $countryGeoNameId,
        public readonly bool $isEu,
        public readonly Continent $continent,
        public readonly int $continentGeoNameId,
        public readonly Coordinates $coordinates,
        public readonly array $security,
        public readonly array $timeZone,
        public readonly array $flag,
        public readonly array $currency,
        public readonly array $connection,
    ) {
    }
}
