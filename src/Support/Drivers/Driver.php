<?php

declare(strict_types=1);

namespace XbNz\Resolver\Support\Drivers;

use Illuminate\Support\Collection;
use Psr\Http\Message\RequestInterface;
use XbNz\Resolver\Support\DTOs\DriverConsumableDTO;

interface Driver
{
    /**
     * @param array<DriverConsumableDTO> $dataObjects
     * @return Collection<RequestInterface>
     */
    public function getRequests(array $dataObjects): Collection;

    public function supports(string $driver): bool;
}
