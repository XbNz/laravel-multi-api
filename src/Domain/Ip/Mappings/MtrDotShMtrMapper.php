<?php

declare(strict_types=1);

namespace XbNz\Resolver\Domain\Ip\Mappings;

use Illuminate\Support\Collection;
use XbNz\Resolver\Domain\Ip\Actions\MtrProbeSearchAction;
use XbNz\Resolver\Domain\Ip\Drivers\MtrDotShMtrDriver;
use XbNz\Resolver\Domain\Ip\DTOs\MtrDotSh\MtrDotShMtrResultsData;
use XbNz\Resolver\Factories\Ip\IpDataFactory;
use XbNz\Resolver\Factories\Ip\MtrDotShMtrHopResultsFactory;
use XbNz\Resolver\Support\DTOs\RawResultsData;
use XbNz\Resolver\Support\Mappings\Mapper;

class MtrDotShMtrMapper implements Mapper
{
    public function __construct(
        private MtrProbeSearchAction $searchAction,
    ) {
    }

    public function map(RawResultsData $rawIpResults): MtrDotShMtrResultsData
    {
        $hops = Collection::make($rawIpResults->data['hops'])
            ->map(fn (array $hop, string $hopPosition) => MtrDotShMtrHopResultsFactory::fromRawHop($hop, (int) $hopPosition))
            ->values();

        return new MtrDotShMtrResultsData(
            $this->searchAction->execute(searchTerm: $rawIpResults->data['probe_id'])->sole(),
            IpDataFactory::fromIp($rawIpResults->data['target_ip']),
            $hops
        );
    }

    public function supports(string $driver): bool
    {
        return $driver === MtrDotShMtrDriver::class;
    }
}
