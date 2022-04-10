<?php

namespace XbNz\Resolver\Domain\Ip\DTOs;

class RawIpResultsData
{
    public function __construct(
        public readonly string $provider,
        public readonly array $data,
    )
    {}
}