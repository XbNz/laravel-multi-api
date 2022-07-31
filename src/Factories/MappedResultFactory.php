<?php

declare(strict_types=1);

namespace XbNz\Resolver\Factories;

use Illuminate\Support\Collection;
use Webmozart\Assert\Assert;
use XbNz\Resolver\Domain\Ip\Services\Request;
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

    /**
     * @param class-string<Request> $request
     */
    public function forRequest(string $request, RequestResponseWrapper $guzzleDuo)
    {
        Assert::classExists($request);

        return Collection::make($this->mappers)
            ->sole(fn (Mapper $mapper) => $mapper->supports($request))
            ->map($guzzleDuo);
    }
}
