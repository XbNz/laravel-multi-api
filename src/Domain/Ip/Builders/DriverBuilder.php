<?php

namespace XbNz\Resolver\Domain\Ip\Builders;

use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Collection;
use Illuminate\Support\ItemNotFoundException;
use XbNz\Resolver\Domain\Ip\Actions\CollectEligibleDriversAction;
use XbNz\Resolver\Domain\Ip\Actions\CreateCollectionFromQueriedIpDataAction;
use XbNz\Resolver\Domain\Ip\Actions\VerifyIpIntegrityAction;
use XbNz\Resolver\Domain\Ip\Collections\IpCollection;
use XbNz\Resolver\Support\Drivers\Driver;
use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Support\Exceptions\DriverNotFoundException;

class DriverBuilder
{
    private Collection $chosenDrivers;
    private Collection $allDrivers;
    private IpData $ipData;

    public function __construct(
        array $drivers,
        private VerifyIpIntegrityAction $verifyIpIntegrity,
        private CreateCollectionFromQueriedIpDataAction $collectionFromQueriedIpDataAction,
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

    public function ipGeolocationDotIo()
    {
        try {
            $ipGeolocationDriver = $this->allDrivers
                ->firstOrFail(function ($value, $key){
                    return $value->supports() === 'ipGeolocationDotIo';
                });
        } catch (ItemNotFoundException $e) {
            throw new DriverNotFoundException(
                "The requested driver for ipGeolocationDotIo was not discoverable"
            );
        }

        $this->chosenDrivers[] = $ipGeolocationDriver;
        return $this;
    }

    public function normalize(): IpCollection
    {
        $queriedResults = collect();
        $this->chosenDrivers->map(function (Driver $driver) use (&$queriedResults){
            $queriedResults[] = $driver->query($this->ipData);
        });
        return $this->collectionFromQueriedIpDataAction
            ->execute($queriedResults);
    }

    public function raw(): Collection
    {
        $rawResults = collect();
        $this->chosenDrivers->map(function (Driver $driver) use (&$rawResults) {
            $rawResults[] = $driver->raw($this->ipData);
        });
        return $rawResults;
    }

    public function withIp(string $ip)
    {
        $this->ipData = $this->verifyIpIntegrity->execute($ip);
        return $this;
    }

}