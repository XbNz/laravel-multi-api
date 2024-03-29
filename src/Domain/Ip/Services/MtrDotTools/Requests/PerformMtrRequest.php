<?php

declare(strict_types=1);

namespace XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Requests;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\DTOs\MtrDotToolsProbeData;

class PerformMtrRequest
{
    public const URI = 'https://mtr.tools';

    public static function generate(MtrDotToolsProbeData $probe, IpData $ipData): \GuzzleHttp\Psr7\Request
    {
        $uri = new Uri(self::URI);
        $uri = $uri->withPath("/{$probe->probeId}/mtr/{$ipData->ip}");
        return new Request('GET', $uri);
    }
}
