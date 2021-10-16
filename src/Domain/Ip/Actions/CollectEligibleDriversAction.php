<?php

namespace XbNz\Resolver\Domain\Ip\Actions;

use Illuminate\Support\Collection;
use XbNz\Resolver\Domain\Ip\Drivers\Driver;

class CollectEligibleDriversAction
{
    public function __construct(
        private array                     $drivers,
        private GetApiKeysForDriverAction $driversWithApiKeys,
        private CollectDriversWithFiles   $driversWithFiles,
    )
    {}

    public function execute(): Collection
    {
        $drivers = collect($this->drivers);
        $drivers->map(function (Driver $driver){

        });
    }
}