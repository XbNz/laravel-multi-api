<?php

declare(strict_types=1);

namespace XbNz\Resolver\Domain\Ip\Mappings;

use Illuminate\Support\Collection;
use XbNz\Resolver\Domain\Ip\Actions\MtrProbeSearchAction;
use XbNz\Resolver\Domain\Ip\Drivers\MtrDotShPingDriver;
use XbNz\Resolver\Domain\Ip\DTOs\MtrDotSh\MtrDotShPingResultsData;
use XbNz\Resolver\Domain\Ip\DTOs\MtrDotSh\MtrDotShPingStatisticsResultsData;
use XbNz\Resolver\Factories\Ip\IpDataFactory;
use XbNz\Resolver\Factories\Ip\MtrDotShPingSequenceResultsFactory;
use XbNz\Resolver\Support\DTOs\RequestResponseWrapper;
use XbNz\Resolver\Support\Mappings\Mapper;

class MtrDotShPingMapper implements Mapper
{
    public function __construct(
        private MtrProbeSearchAction $searchAction,
    ) {
    }

    public function map(RequestResponseWrapper $rawIpResults): MtrDotShPingResultsData
    {
        $sequences = Collection::make($rawIpResults->data['sequences'])
            ->map(fn (array $sequence, string $sequencePosition) => MtrDotShPingSequenceResultsFactory::fromRawSequence($sequence, (int) $sequencePosition))
            ->values();

        if ($rawIpResults->data['statistics'] !== null) {
            $statistics = new MtrDotShPingStatisticsResultsData(
                (float) $rawIpResults->data['statistics']['minimum_rtt'],
                (float) $rawIpResults->data['statistics']['average_rtt'],
                (float) $rawIpResults->data['statistics']['maximum_rtt'],
                (float) $rawIpResults->data['statistics']['jitter'],
            );
        }

        return new MtrDotShPingResultsData(
            $this->searchAction->execute(searchTerm: '1F7As')->sole(),
            IpDataFactory::fromIp($rawIpResults->data['target_ip']),
            $rawIpResults->data['packet_loss'],
            $sequences,
            $statistics ?? null,
        );
    }

    public function supports(string $request): bool
    {
        return $request === MtrDotShPingDriver::class;
    }
}
