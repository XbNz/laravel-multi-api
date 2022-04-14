<?php

namespace XbNz\Resolver\Tests\Feature\Fakes;

use Illuminate\Support\Collection;
use XbNz\Resolver\Support\Drivers\Driver;

class FakeDriver implements Driver
{

    public function getRequests(array $ipDataObjects): Collection
    {
        // TODO: Implement getRequests() method.
    }

    public function supports(string $driver): bool
    {
        return $driver === __CLASS__;
    }
}