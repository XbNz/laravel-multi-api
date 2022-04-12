<?php

namespace XbNz\Resolver\Domain\Ip\Builders;

use Illuminate\Support\Collection;
use XbNz\Resolver\Domain\Ip\Actions\CollectEligibleDriversAction;
use XbNz\Resolver\Domain\Ip\Actions\CreateCollectionFromQueriedIpDataAction;
use XbNz\Resolver\Domain\Ip\Actions\FetchRawDataForIpsAction;
use XbNz\Resolver\Domain\Ip\Actions\VerifyIpIntegrityAction;
use XbNz\Resolver\Domain\Ip\Collections\IpCollection;
use XbNz\Resolver\Domain\Ip\Drivers\AbuseIpDbDotComDriver;
use XbNz\Resolver\Domain\Ip\Drivers\IpApiDotComDriver;
use XbNz\Resolver\Domain\Ip\Drivers\IpDataDotCoDriver;
use XbNz\Resolver\Domain\Ip\Drivers\IpGeolocationDotIoDriver;
use XbNz\Resolver\Domain\Ip\Drivers\IpInfoDotIoDriver;
use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Domain\Ip\DTOs\NormalizedGeolocationResultsData;
use XbNz\Resolver\Domain\Ip\DTOs\RawIpResultsData;
use XbNz\Resolver\Factories\Ip\NormalizedIpResultsDataFactory;

class DriverBuilder
{
    /**
     * @var Collection<string> $chosenDrivers
     * @var Collection<IpData> $chosenIps
     */
    private Collection $chosenDrivers;
    private Collection $chosenIps;

    public function __construct(
        private VerifyIpIntegrityAction $verifyIpIntegrity,
        private FetchRawDataForIpsAction $fetchRawDataForIps,
        private NormalizedIpResultsDataFactory $normalizedResultsFactory,
    ) {
        $this->chosenDrivers = collect();
        $this->chosenIps = collect();
    }

    public function ipInfoDotIo(): static
    {
        $this->chosenDrivers[] = IpInfoDotIoDriver::class;
        return $this;
    }

    public function ipGeolocationDotIo(): static
    {
        $this->chosenDrivers[] = IpGeolocationDotIoDriver::class;
        return $this;
    }

    public function ipApiDotCom(): static
    {
        $this->chosenDrivers[] = IpApiDotComDriver::class;
        return $this;
    }

    public function ipDataDotCo(): static
    {
        $this->chosenDrivers[] = IpDataDotCoDriver::class;
        return $this;
    }

    public function abuseIpDbDotCom(): static
    {
        $this->chosenDrivers[] = AbuseIpDbDotComDriver::class;
        return $this;
    }

    /**
     * @param array<string> $drivers Provider names in string format: e.g [IpGeolocationDotIoDriver::class, IpInfoDotIo::class]
     */
    public function withDrivers(array $drivers): static
    {
        $this->chosenDrivers->push($drivers);
        return $this;
    }


    /**
     * @return array<NormalizedGeolocationResultsData>
     */
    public function normalize(): array
    {
        return Collection::make($this->raw())
            ->map(fn (RawIpResultsData $rawData) => $this->normalizedResultsFactory->fromRaw($rawData))
            ->toArray();
    }

    /**
     * @return array<RawIpResultsData>
     */
    public function raw(): array
    {
        return $this->fetchRawDataForIps->execute(
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
            $this->chosenIps->push($this->verifyIpIntegrity->execute($ip));
        }

        return $this;
    }

}