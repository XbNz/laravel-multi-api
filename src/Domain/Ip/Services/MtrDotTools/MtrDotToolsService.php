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
use XbNz\Resolver\Support\Helpers\Async;
use XbNz\Resolver\Support\Mappings\Mapper;

class MtrDotToolsService
{


    public function __construct(
        private readonly Client $client,
        private readonly ListAllProbesRequest $listAllProbesRequest,
        private readonly PerformMtrRequest $performMtrRequest,
    ) {
    }


    /**
     * @return ProbesCollection<MtrDotToolsProbeData>
     * @throws MtrDotToolsException
     */
    public function listProbes(?callable $intercept = null): ProbesCollection
    {
        try {
            $response = $this->client->send(
                ($this->listAllProbesRequest)()
            );
        } catch (GuzzleException $exception) {
            if ($exception instanceof RequestException) {
                MtrDotToolsException::fromRequestException($exception);
            }

            throw $exception;
        }

        if ($intercept !== null) {
            $intercept($response);
        }

        return ListAllProbesMapper::map(new RequestResponseWrapper(
            ($this->listAllProbesRequest)(),
            $response
        ));
    }

    /**
     * @param array<IpData> $ipData
     * @param array<MtrDotToolsProbeData> $probes
     * @return MtrResultsCollection<MtrResultData>
     */
    public function mtr(array $ipData, array $probes = []): MtrResultsCollection
    {
        Assert::allIsInstanceOf($ipData, IpData::class);
        Assert::allIsInstanceOf($probes, MtrDotToolsProbeData::class);


        // TODO: Change config logic and fix this
//        Collection::make(Config::get())
//        $probes = $this->listProbes()
//            ->fuzzySearch('');

        $requests = Collection::make($ipData)
            ->map(function (IpData $ipDataObject) use ($probes) {
                return Collection::make($probes)
                    ->map(fn(MtrDotToolsProbeData $probe) => ($this->performMtrRequest)($probe, $ipDataObject));
            });

        $responses = Async::withClient($this->client, $requests->toArray());

        return MtrResultsCollection::make($responses)
            ->map(fn (RequestResponseWrapper $wrapper) => PerformMtrMapper::map($wrapper));
    }

}