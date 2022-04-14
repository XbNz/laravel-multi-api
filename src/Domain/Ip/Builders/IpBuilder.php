<?php

namespace XbNz\Resolver\Domain\Ip\Builders;

use Illuminate\Support\Collection;
use XbNz\Resolver\Domain\Ip\Actions\CollectEligibleDriversAction;
use XbNz\Resolver\Domain\Ip\Actions\CreateCollectionFromQueriedIpDataAction;
use XbNz\Resolver\Domain\Ip\Collections\IpCollection;
use XbNz\Resolver\Domain\Ip\Drivers\AbuseIpDbDotComDriver;
use XbNz\Resolver\Domain\Ip\Drivers\IpApiDotComDriver;
use XbNz\Resolver\Domain\Ip\Drivers\IpDataDotCoDriver;
use XbNz\Resolver\Domain\Ip\Drivers\IpGeolocationDotIoDriver;
use XbNz\Resolver\Domain\Ip\Drivers\IpInfoDotIoDriver;
use XbNz\Resolver\Domain\Ip\Drivers\MtrDotShMtrDriver;
use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Domain\Ip\DTOs\NormalizedGeolocationResultsData;
use XbNz\Resolver\Factories\Ip\IpDataFactory;
use XbNz\Resolver\Factories\MappedResultFactory;
use XbNz\Resolver\Support\Actions\FetchRawDataAction;
use XbNz\Resolver\Support\Drivers\Driver;
use XbNz\Resolver\Support\DTOs\MappableDTO;
use XbNz\Resolver\Support\DTOs\RawResultsData;

class IpBuilder
{
    /**
     * @var Collection<string> $chosenDrivers
     * @var Collection<IpData> $chosenIps
     * @var Collection<Driver> $drivers
     */
    private Collection $chosenDrivers;
    private Collection $chosenIps;
    private Collection $drivers;


    public function __construct(
        private FetchRawDataAction      $fetchRawData,
        private MappedResultFactory     $mapperResultFactory,
        array                           $drivers,
    ) {
        $this->chosenDrivers = collect();
        $this->chosenIps = collect();
        $this->drivers = collect($drivers);
    }

    public function ipInfoDotIo(): static
    {
        $this->chosenDrivers[] = $this->drivers->sole(fn (Driver $driver)
            => $driver->supports(IpInfoDotIoDriver::class)
        );
        return $this;
    }

    public function ipGeolocationDotIo(): static
    {
        $this->chosenDrivers[] = $this->drivers->sole(fn (Driver $driver)
            => $driver->supports(IpGeolocationDotIoDriver::class)
        );
        return $this;
    }

    public function ipApiDotCom(): static
    {
        $this->chosenDrivers[] = $this->drivers->sole(fn (Driver $driver)
            => $driver->supports(IpApiDotComDriver::class)
        );
        return $this;
    }

    public function ipDataDotCo(): static
    {
        $this->chosenDrivers[] = $this->drivers->sole(fn (Driver $driver)
            => $driver->supports(IpDataDotCoDriver::class)
        );
        return $this;
    }

    public function abuseIpDbDotCom(): static
    {
        $this->chosenDrivers[] = $this->drivers->sole(fn (Driver $driver)
            => $driver->supports(AbuseIpDbDotComDriver::class)
        );
        return $this;
    }

    public function mtrDotShMtr(): static
    {
        $this->chosenDrivers[] = $this->drivers->sole(fn (Driver $driver)
            => $driver->supports(MtrDotShMtrDriver::class)
        );
        return $this;
    }

    /**
     * @param array<string> $drivers Provider names in string format: e.g [IpGeolocationDotIoDriver::class, IpInfoDotIo::class]
     */
    public function withDrivers(array $drivers): static
    {
        foreach ($drivers as $driver) {
            $this->chosenDrivers[] = $this->drivers->sole(fn (Driver $iocDriver)
                => $iocDriver->supports($driver)
            );
        }

        return $this;
    }


    /**
     * @return array<MappableDTO>
     */
    public function normalize(): array
    {
        return Collection::make($this->raw())
            ->map(fn (RawResultsData $rawData) => $this->mapperResultFactory->fromRaw($rawData))
            ->toArray();
    }

    /**
     * @return array<RawResultsData>
     */
    public function raw(): array
    {
        return $this->fetchRawData->execute(
                $this->chosenIps->toArray(),
                $this->chosenDrivers->toArray()
            );
    }

    /**
     * @param array<string> $ips IPv4 or IPv6 addresses in string format: e.g ['1.1.1.1', '9.9.9.9']
     */
    public function withIps(array $ips): static
    {
        foreach ($ips as $ip) {
            $this->chosenIps->push(IpDataFactory::fromIp($ip));
        }

        return $this;
    }

}