<?php

declare(strict_types=1);

namespace XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Mappers;

use Illuminate\Support\Collection;
use XbNz\Resolver\Domain\Ip\Actions\ConvertPingPlainToJsonAction;
use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Domain\Ip\DTOs\MtrDotSh\RekindledMtrData;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Collections\ProbesCollection;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\DTOs\MtrDotToolsPingResultsData;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\DTOs\MtrDotToolsPingSequenceResultsData;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\DTOs\MtrDotToolsPingStatisticsResultsData;
use XbNz\Resolver\Support\DTOs\RequestResponseWrapper;

class PerformPingMapper
{
    public function __construct(
        private readonly ConvertPingPlainToJsonAction $convertPingPlainToJsonAction,
    ) {
    }

    public function map(RequestResponseWrapper $requestResponse, ProbesCollection $probesCollection): MtrDotToolsPingResultsData
    {
        $plainTextResponse = $requestResponse->guzzleResponse->getBody()->getContents();

        [, $probeId, , $ip] = explode('/', $requestResponse->guzzleRequest->getUri()->getPath());
        $ipData = IpData::fromIp($ip);

        $probe = $probesCollection->findById($probeId);

        $json = $this->convertPingPlainToJsonAction->execute(
            new RekindledMtrData($plainTextResponse, $probe->probeId, $ip),
        );

        $arrayOfPingData = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        $sequences = Collection::make($arrayOfPingData['sequences'])
            ->map(fn (array $sequence) => MtrDotToolsPingSequenceResultsData::fromRaw($sequence))
            ->toArray();

        return new MtrDotToolsPingResultsData(
            $probe,
            $ipData,
            $arrayOfPingData['packet_loss'],
            $sequences,
            new MtrDotToolsPingStatisticsResultsData(
                $arrayOfPingData['statistics']['minimum_rtt'],
                $arrayOfPingData['statistics']['average_rtt'],
                $arrayOfPingData['statistics']['maximum_rtt'],
                $arrayOfPingData['statistics']['jitter'],
            ),
        );
    }
}
