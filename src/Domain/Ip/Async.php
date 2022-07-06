<?php

namespace XbNz\Resolver\Domain\Ip;

use XbNz\Resolver\Domain\Ip\Services\Request;
use XbNz\Resolver\Factories\GuzzleClientFactory;

class Async
{
    public function __construct(
        private readonly GuzzleClientFactory $guzzleClientFactory,
    ) {
    }

    /**
     * @param array<Request> $requests
     */
    public function fulfill(array $requests): self
    {
        $client = $this->guzzleClientFactory->for()
    }
}