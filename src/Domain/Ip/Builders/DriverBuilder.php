<?php

namespace XbNz\Resolver\Domain\Ip\Builders;

use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Collection;
use XbNz\Resolver\Domain\Ip\Actions\CollectEligibleDriversAction;
use XbNz\Resolver\Domain\Ip\Actions\CreateCollectionFromQueriedIpDataAction;
use XbNz\Resolver\Domain\Ip\Actions\VerifyIpIntegrityAction;
use XbNz\Resolver\Domain\Ip\Collections\IpCollection;
use XbNz\Resolver\Domain\Ip\DTOs\IpData;

class DriverBuilder
{
    private Collection $availableDrivers;

    private bool $ipApi = false;

    public function __construct(
        CollectEligibleDriversAction $driversAction,
        VerifyIpIntegrityAction $verifyIpIntegrity,
        private CreateCollectionFromQueriedIpDataAction $collectionFromQueriedIpDataAction,
    )
    {

    }

    public function ipApi()
    {

        $this->ipApi = true;

        return $this;
    }

    public static function fromDto(IpData $ipData): self
    {

    }



    public function execute(string $ip): object
    {
        $pipes = [];

        if ($this->ipApi) {
            $pipes[] = IpApi::class;
        }

        return app(Pipeline::class)
            ->send($ip)
            ->through($pipes)
            ->thenReturn();
    }

}