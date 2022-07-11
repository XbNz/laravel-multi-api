<?php

namespace XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Requests;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\DTOs\MtrDotToolsProbeData;

class PerformPingRequest implements \XbNz\Resolver\Domain\Ip\Services\Request
{
    public const URI = 'https://mtr.tools';

    public function __invoke(MtrDotToolsProbeData $probe, IpData $ipData): Request
    {
        $uri = new Uri(self::URI);
        $uri = $uri->withPath("/{$probe->probeId}/ping/{$ipData->ip}");
        return new Request('GET', $uri);
    }
}