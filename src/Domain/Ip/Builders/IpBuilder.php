<?php

declare(strict_types=1);

namespace XbNz\Resolver\Domain\Ip\Builders;

use Illuminate\Support\Collection;
use XbNz\Resolver\Domain\Ip\Drivers\AbstractApiDotComDriver;
use XbNz\Resolver\Domain\Ip\Drivers\AbuseIpDbDotComDriver;
use XbNz\Resolver\Domain\Ip\Drivers\IpApiDotCoDriver;
use XbNz\Resolver\Domain\Ip\Drivers\IpApiDotComDriver;
use XbNz\Resolver\Domain\Ip\Drivers\IpDashApiDotComDriver;
use XbNz\Resolver\Domain\Ip\Drivers\IpDataDotCoDriver;
use XbNz\Resolver\Domain\Ip\Drivers\IpGeolocationDotIoDriver;
use XbNz\Resolver\Domain\Ip\Drivers\IpInfoDotIoDriver;
use XbNz\Resolver\Domain\Ip\Drivers\MtrDotShMtrDriver;
use XbNz\Resolver\Domain\Ip\Drivers\MtrDotShPingDriver;
use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Factories\Ip\IpDataFactory;
use XbNz\Resolver\Factories\MappedResultFactory;
use XbNz\Resolver\Support\Actions\FetchRawDataAction;
use XbNz\Resolver\Support\Drivers\Driver;
use XbNz\Resolver\Support\DTOs\Mappable;
use XbNz\Resolver\Support\DTOs\RequestResponseWrapper;

class IpBuilder
{
    /**
     * @var Collection<Driver>
     */
    private Collection $chosenDrivers;

    /**
     * @var Collection<IpData>
     */
    private Collection $chosenIps;

    /**
     * @var Collection<Driver>
     */
    private Collection $drivers;

    /**
     * @param array<Driver> $drivers
     */
    public function __construct(
        private readonly FetchRawDataAction $fetchRawData,
        private readonly MappedResultFactory $mapperResultFactory,
        array $drivers,
    ) {
        $this->chosenDrivers = Collection::make();
        $this->chosenIps = Collection::make();
        $this->drivers = Collection::make($drivers);
    }

    public function mtrTools()
    {


        return $this;
    }

    public function ipInfoDotIo(): static
    {
        $this->chosenDrivers[] = $this->drivers->sole(
            fn (Driver $driver)
            => $driver->supports(IpInfoDotIoDriver::class)
        );
        return $this;
    }

    public function ipGeolocationDotIo(): static
    {
        $this->chosenDrivers[] = $this->drivers->sole(
            fn (Driver $driver)
            => $driver->supports(IpGeolocationDotIoDriver::class)
        );
        return $this;
    }

    public function ipApiDotCom(): static
    {
        $this->chosenDrivers[] = $this->drivers->sole(
            fn (Driver $driver)
            => $driver->supports(IpApiDotComDriver::class)
        );
        return $this;
    }

    public function ipDataDotCo(): static
    {
        $this->chosenDrivers[] = $this->drivers->sole(
            fn (Driver $driver)
            => $driver->supports(IpDataDotCoDriver::class)
        );
        return $this;
    }

    public function abuseIpDbDotCom(): static
    {
        $this->chosenDrivers[] = $this->drivers->sole(
            fn (Driver $driver)
            => $driver->supports(AbuseIpDbDotComDriver::class)
        );
        return $this;
    }

    public function mtrDotShMtr(): static
    {
        $this->chosenDrivers[] = $this->drivers->sole(
            fn (Driver $driver)
            => $driver->supports(MtrDotShMtrDriver::class)
        );
        return $this;
    }

    public function mtrDotShPing(): static
    {
        $this->chosenDrivers[] = $this->drivers->sole(
            fn (Driver $driver)
            => $driver->supports(MtrDotShPingDriver::class)
        );
        return $this;
    }

    public function ipDashApiDotCom(): static
    {
        $this->chosenDrivers[] = $this->drivers->sole(
            fn (Driver $driver)
            => $driver->supports(IpDashApiDotComDriver::class)
        );
        return $this;
    }

    public function ipApiDotCo(): static
    {
        $this->chosenDrivers[] = $this->drivers->sole(
            fn (Driver $driver)
            => $driver->supports(IpApiDotCoDriver::class)
        );
        return $this;
    }

    public function abstractApiDotCom(): static
    {
        $this->chosenDrivers[] = $this->drivers->sole(
            fn (Driver $driver)
            => $driver->supports(AbstractApiDotComDriver::class)
        );
        return $this;
    }

    /**
     * @param array<class-string<Driver>> $drivers Provider names in string format: e.g [IpGeolocationDotIoDriver::class, IpInfoDotIo::class]
     */
    public function withDrivers(array $drivers): static
    {
        foreach ($drivers as $driver) {
            $this->chosenDrivers[] = $this->drivers->sole(
                fn (Driver $iocDriver)
                => $iocDriver->supports($driver)
            );
        }

        return $this;
    }

    /**
     * @return array<Mappable>
     */
    public function normalize(): array
    {
        return Collection::make($this->raw())
            ->map(fn (RequestResponseWrapper $rawData) => $this->mapperResultFactory->fromRaw($rawData))
            ->toArray();
    }

    /**
     * @return array<RequestResponseWrapper>
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
