<?php

namespace XbNz\Resolver\Domain\Ip\Builders;

use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Collection;
use Illuminate\Support\ItemNotFoundException;
use XbNz\Resolver\Domain\Ip\Actions\CollectEligibleDriversAction;
use XbNz\Resolver\Domain\Ip\Actions\CreateCollectionFromQueriedIpDataAction;
use XbNz\Resolver\Domain\Ip\Actions\VerifyIpIntegrityAction;
use XbNz\Resolver\Domain\Ip\Collections\IpCollection;
use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Support\Exceptions\DriverNotFoundException;

class DriverBuilder
{
    private Collection $chosenDrivers;
    private Collection $allDrivers;

    public function __construct(
        array $drivers,
        private VerifyIpIntegrityAction $verifyIpIntegrity,
        private Pipeline $pipeline
//        private CreateCollectionFromQueriedIpDataAction $collectionFromQueriedIpDataAction,
    )
    {
        $this->allDrivers = collect($drivers);
        $this->chosenDrivers = collect();
    }

    public function ipInfo()
    {
        try {
            $ipInfoDriver = $this->allDrivers
                ->firstOrFail(function ($value, $key){
                    return $value->supports() === 'ipInfo';
                });
        } catch (ItemNotFoundException $e) {
            throw new DriverNotFoundException(
                "The requested driver for ipInfo was not discoverable"
            );
        }

        $this->chosenDrivers[] = $ipInfoDriver;
        return $this;
    }

    public function ipGeolocation()
    {
        try {
            $ipGeolocationDriver = $this->allDrivers
                ->firstOrFail(function ($value, $key){
                    return $value->supports() === 'ipGeolocation';
                });
        } catch (ItemNotFoundException $e) {
            throw new DriverNotFoundException(
                "The requested driver for ipGeolocation was not discoverable"
            );
        }

        $this->chosenDrivers[] = $ipGeolocationDriver;
        return $this;
    }

    public function execute(string $ip)
    {
        $ipData = $this->verifyIpIntegrity->execute($ip);
        $pipes = $this->chosenDrivers->toArray();

        $test = $this->pipeline
            ->send($ipData)
            ->through($pipes)
            ->via('query')
            ->thenReturn();

    }

}