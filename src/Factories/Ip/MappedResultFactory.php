<?php

namespace XbNz\Resolver\Factories\Ip;


use Illuminate\Support\Collection;
use XbNz\Resolver\Domain\Ip\DTOs\MappableDTO;
use XbNz\Resolver\Domain\Ip\DTOs\NormalizedGeolocationResultsData;
use XbNz\Resolver\Domain\Ip\DTOs\RawIpResultsData;
use XbNz\Resolver\Domain\Ip\Mappings\Mapper;

class MappedResultFactory
{
    /**
     * @param array<Mapper> $mappers
     */
    public function __construct(
        private array $mappers
    )
    {}

    public function fromRaw(RawIpResultsData $ipResultsData): MappableDTO
    {
        return Collection::make($this->mappers)
            ->sole(fn (Mapper $mapper) => $mapper->supports($ipResultsData->provider))
            ->map($ipResultsData);
    }

}