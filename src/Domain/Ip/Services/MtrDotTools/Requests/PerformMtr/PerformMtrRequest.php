<?php

namespace XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Requests\PerformMtr;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\DTOs\MtrDotToolsProbeData;

class PerformMtrRequest implements \XbNz\Resolver\Domain\Ip\Services\Request
{
    public const URI = 'https://mtr.tools';
    public const HEADERS = [
        'Accept' => 'application/json',
    ];

    public function __invoke(MtrDotToolsProbeData $probe, IpData $ipData): Request
    {
        $uri = new Uri(self::URI);
        $uri = $uri->withPath("/{$probe->probeId}/mtr/{$ipData->ip}");
        return new Request('GET', $uri, self::HEADERS);
    }
}