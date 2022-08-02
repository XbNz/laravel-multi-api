<?php

declare(strict_types=1);

namespace XbNz\Resolver\Domain\Ip\Services\IpGeolocationDotIo\Requests;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use XbNz\Resolver\Domain\Ip\DTOs\IpData;

class GeolocationRequest
{
    public const URI = 'https://api.ipgeolocation.io';

    public const PATH = '/ipgeo';

    public const HEADERS = [
        'Accept' => 'application/json',
    ];

    public static function generate(IpData $ipData): \GuzzleHttp\Psr7\Request
    {
        $uri = new Uri(self::URI);
        $uri = $uri->withPath(self::PATH);
        $uri = Uri::withQueryValue($uri, 'ip', $ipData->ip);

        return new Request('GET', $uri, self::HEADERS);
    }
}
