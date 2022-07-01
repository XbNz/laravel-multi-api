<?php

namespace XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Requests;

class PerformMtrRequest
{
    public const URI = 'https://mtr.tools';
    public const PATH = '/probes.json';
    public const HEADERS = [
        'Accept' => 'application/json',
    ];

    public function __invoke(): Request
    {
        $uri = new Uri(self::URI);
        $uri = $uri->withPath(self::PATH);
        return new Request('GET', $uri, self::HEADERS);
    }
}