<?php

declare(strict_types=1);

namespace XbNz\Resolver\Factories;

use Illuminate\Support\Collection;
use XbNz\Resolver\Support\DTOs\Mappable;
use XbNz\Resolver\Support\DTOs\RequestResponseWrapper;
use XbNz\Resolver\Support\Mappings\Mapper;

class MappedResultFactory
{
    /**
     * @param array<Mapper> $mappers
     */
    public function __construct(
        private array $mappers
    ) {
    }

    public function fromRaw(RequestResponseWrapper $rawDataDto): Mappable
    {
        return Collection::make($this->mappers)
            ->sole(fn (Mapper $mapper) => $mapper->supports($rawDataDto->request))
            ->map($rawDataDto);
    }
}
