<?php

namespace XbNz\Resolver\Domain\Ip\Builders;

use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Collection;
use Illuminate\Support\ItemNotFoundException;
use XbNz\Resolver\Domain\Ip\Actions\CollectEligibleDriversAction;
use XbNz\Resolver\Domain\Ip\Actions\CreateCollectionFromQueriedIpDataAction;
use XbNz\Resolver\Domain\Ip\Actions\FetchRawDataForIpsAction;
use XbNz\Resolver\Domain\Ip\Actions\VerifyIpIntegrityAction;
use XbNz\Resolver\Domain\Ip\Collections\IpCollection;
use XbNz\Resolver\Domain\Ip\Drivers\IpApiDotComDriver;
use XbNz\Resolver\Domain\Ip\Drivers\IpDataDotCoDriver;
use XbNz\Resolver\Domain\Ip\Drivers\IpGeolocationDotIoDriver;
use XbNz\Resolver\Domain\Ip\Drivers\IpInfoDotIoDriver;
use XbNz\Resolver\Domain\Ip\DTOs\NormalizedIpResultsData;
use XbNz\Resolver\Domain\Ip\DTOs\RawIpResultsData;
use XbNz\Resolver\Factories\NormalizedIpResultsDataFactory;
use XbNz\Resolver\Support\Drivers\Driver;
use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Support\Exceptions\DriverNotFoundException;

class DriverBuilder
{
    /**
     * @var Collection<string> $chosenDrivers
     * @var Collection<IpData> $chosenIps
     */
    private Collection $chosenProviders;
    private Collection $chosenIps;

    public function __construct(
        private VerifyIpIntegrityAction $verifyIpIntegrity,
        private FetchRawDataForIpsAction $fetchRawDataForIps,
        private NormalizedIpResultsDataFactory $normalizedResultsFactory,
    ) {
        $this->chosenProviders = collect();
        $this->chosenIps = collect();
    }

    public function ipInfoDotIo(): static
    {
        $this->chosenProviders[] = 'ipinfo.io';
        return $this;
    }

    public function ipGeolocationDotIo(): static
    {
        $this->chosenProviders[] = 'ipgeolocation.io';
        return $this;
    }

    public function ipApiDotCom(): static
    {
        $this->chosenProviders[] = 'ipapi.com';
        return $this;
    }

    public function ipDataDotCo(): static
    {
        $this->chosenProviders[] = 'ipdata.co';
        return $this;
    }

    public function abuseIpDbDotCom(): static
    {
        $this->chosenProviders[] = 'abuseipdb.com';
        return $this;
    }

    /**
     * @param array<string> $providers Provider names in string format: e.g ['ipgeolocation.io', 'ipinfo.io']
     */
    public function withProviders(array $providers): static
    {
        $this->chosenProviders->push($providers);
        return $this;
    }


    /**
     * @return array<NormalizedIpResultsData>
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
                $this->chosenProviders->toArray()
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