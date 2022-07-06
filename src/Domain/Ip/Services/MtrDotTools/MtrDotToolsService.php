<?php

namespace XbNz\Resolver\Domain\Ip\Services\MtrDotTools;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use JsonException;
use Webmozart\Assert\Assert;
use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Domain\Ip\DTOs\MtrDotSh\MtrResultData;
use XbNz\Resolver\Domain\Ip\DTOs\MtrDotSh\MtrDotShPingResultsData;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Collections\MtrResultsCollection;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Collections\ProbesCollection;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\DTOs\MtrDotToolsProbeData;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Exceptions\MtrDotToolsException;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Mappers\ListAllProbesMapper;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Mappers\PerformMtrMapper;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Requests\ListAllProbes\ListAllProbesRequest;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Requests\PerformMtr\PerformMtrRequest;
use XbNz\Resolver\Factories\MappedResultFactory;
use XbNz\Resolver\Support\DTOs\RequestResponseWrapper;
use XbNz\Resolver\Support\Helpers\Send;
use XbNz\Resolver\Support\Mappings\Mapper;

class MtrDotToolsService
{

    public function __construct(
        private readonly Client $client,

        private readonly ListAllProbesRequest $listAllProbesRequest,
        private readonly ListAllProbesMapper $listAllProbesMapper,

        private readonly PerformMtrRequest $performMtrRequest,
        private readonly PerformMtrMapper $performMtrMapper,
    ) {
    }


    /**
     * @return ProbesCollection<MtrDotToolsProbeData>
     * @throws MtrDotToolsException
     */
    public function listProbes(?callable $intercept = null): ProbesCollection
    {
        $response = Send::sync(
            $this->client,
            ($this->listAllProbesRequest)()
        );

        if ($intercept !== null) {
            $intercept($response);
        }

        return $this->listAllProbesMapper->map($response);
    }


    /**
     * @param array<IpData> $ipData
     * @param ProbesCollection<MtrDotToolsProbeData> $probes
     * @return MtrResultsCollection<MtrResultData>
     */
    public function mtr(array $ipData, ProbesCollection $probes = new ProbesCollection()): MtrResultsCollection
    {
        Assert::allIsInstanceOf($ipData, IpData::class);
        Assert::allIsInstanceOf($probes, MtrDotToolsProbeData::class);


        // TODO: Change config logic and fix this


        $requests = Collection::make($ipData)
            ->map(function (IpData $ipDataObject) use ($probes) {
                return Collection::make($probes)
                    ->map(fn(MtrDotToolsProbeData $probe) => ($this->performMtrRequest)($probe, $ipDataObject));
            })->flatten();

        $responses = Send::async($this->client, $requests->toArray());

        return MtrResultsCollection::make($responses)
            ->map(fn (RequestResponseWrapper $wrapper) => $this->performMtrMapper->map($wrapper, $probes));
    }

}