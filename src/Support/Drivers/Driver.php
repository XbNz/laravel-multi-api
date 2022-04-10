<?php

namespace XbNz\Resolver\Support\Drivers;

use Generator;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Prophecy\Promise\PromiseInterface;
use Psr\Http\Message\RequestInterface;
use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Domain\Ip\DTOs\NormalizedIpResultsData;

interface Driver
{
    /**
     * @return Collection<RequestInterface>
     */
    public function getRequests(array $ipDataObjects): Collection;
    public function supports(string $provider): bool;
}