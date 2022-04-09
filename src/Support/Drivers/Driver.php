<?php

namespace XbNz\Resolver\Support\Drivers;

use Generator;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Prophecy\Promise\PromiseInterface;
use Psr\Http\Message\RequestInterface;
use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Domain\Ip\DTOs\QueriedIpData;

interface Driver
{
//    public function query(IpData $ipData): QueriedIpData;
//    public function initiateAsync(IpData $ipData): void;
//    public function resolvePromise(): Response;
//    public function raw(IpData $ipData): array;
//    public function supports(): string;
//    public function requiresApiKey(): bool;
//    public function requiresFile(): bool;


    /**
     * @return Collection<RequestInterface>
     */
    public function getRequests(array $ipDataObjects): Collection;
    public function supports(string $provider): bool;
}