<?php

namespace XbNz\Resolver\Domain\Ip\Actions;

use Illuminate\Support\Collection;

class CollectEligibleDriversAction
{
    public function __construct(private $drivers)
    {}

    public function execute(): Collection
    {

    }
}