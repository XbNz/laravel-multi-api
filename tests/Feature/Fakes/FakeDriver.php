<?php

declare(strict_types=1);

namespace XbNz\Resolver\Tests\Feature\Fakes;

use Illuminate\Support\Collection;
use XbNz\Resolver\Support\Drivers\Driver;

class FakeDriver implements Driver
{
    public function getRequests(array $dataObjects): Collection
    {
        //
    }

    public function supports(string $driver): bool
    {
        return $driver === __CLASS__;
    }
}
