<?php

namespace XbNz\Resolver\Support\DTOs;

class RawResultsData
{
    public function __construct(
        public readonly string $provider,
        public readonly array $data,
    )
    {}
}