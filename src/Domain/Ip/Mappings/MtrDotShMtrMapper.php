<?php

declare(strict_types=1);

namespace XbNz\Resolver\Domain\Ip\Mappings;

use Illuminate\Support\Collection;
use XbNz\Resolver\Domain\Ip\Actions\MtrProbeSearchAction;
use XbNz\Resolver\Domain\Ip\Actions\VerifyIpIntegrityAction;
use XbNz\Resolver\Domain\Ip\Drivers\MtrDotShMtrDriver;
use XbNz\Resolver\Domain\Ip\DTOs\MtrDotShMtrResultsData;
use XbNz\Resolver\Domain\Ip\DTOs\NormalizedGeolocationResultsData;
use XbNz\Resolver\Domain\Ip\DTOs\RawIpResultsData;
use XbNz\Resolver\Factories\Ip\MtrDotShMtrHopResultsFactory;

class MtrDotShMtrMapper implements Mapper
{
    public function __construct(
        private MtrProbeSearchAction $searchAction,
        private VerifyIpIntegrityAction $ipIntegrityAction,
    )
    {}

    public function map(RawIpResultsData $rawIpResults): MtrDotShMtrResultsData
    {
        $hops = Collection::make($rawIpResults->data['hops'])
            ->map(fn (array $hop, string $hopPosition) => MtrDotShMtrHopResultsFactory::fromRawHop($hop, (int) $hopPosition))
            ->values();

        return new MtrDotShMtrResultsData(
            $this->searchAction->execute(searchTerm: $rawIpResults->data['probe_id'])->sole(),
            $this->ipIntegrityAction->execute($rawIpResults->data['target_ip']),
            $hops
        );
    }

    public function supports(string $driver): bool
    {
        return $driver === MtrDotShMtrDriver::class;
    }
}