<?php

namespace XbNz\Resolver\Support\Drivers;

use Illuminate\Http\Client\Response;
use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Domain\Ip\DTOs\QueriedIpData;

interface Driver
{
    public function query(IpData $ipData): QueriedIpData;
    public function initiateAsync(IpData $ipData): void;
    public function resolvePromise(): Response;
    public function raw(IpData $ipData): array;
    public function supports(): string;
    public function requiresApiKey(): bool;
    public function requiresFile(): bool;
}