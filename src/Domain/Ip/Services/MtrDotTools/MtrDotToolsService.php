<?php

declare(strict_types=1);

namespace XbNz\Resolver\Domain\Ip\Services\MtrDotTools;

use GuzzleHttp\ClientInterface;
use Illuminate\Support\Collection;
use Webmozart\Assert\Assert;
use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Collections\MtrResultsCollection;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Collections\PingResultsCollection;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Collections\ProbesCollection;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\DTOs\MtrDotToolsMtrResultsData;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\DTOs\MtrDotToolsPingResultsData;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\DTOs\MtrDotToolsProbeData;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Exceptions\MtrDotToolsException;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Mappers\ListAllProbesMapper;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Mappers\PerformMtrMapper;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Mappers\PerformPingMapper;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Requests\ListAllProbesRequest;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Requests\PerformMtrRequest;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Requests\PerformPingRequest;
use XbNz\Resolver\Domain\Ip\Services\Service;
use XbNz\Resolver\Support\DTOs\RequestResponseWrapper;
use XbNz\Resolver\Support\Helpers\Send;

class MtrDotToolsService implements Service
{
    public function __construct(
        private readonly ClientInterface $client,
        private readonly ListAllProbesRequest $listAllProbesRequest,
        private readonly ListAllProbesMapper $listAllProbesMapper,
        private readonly PerformMtrRequest $performMtrRequest,
        private readonly PerformMtrMapper $performMtrMapper,
        private readonly PerformPingRequest $performPingRequest,
        private readonly PerformPingMapper $performPingMapper,
    ) {
    }

    /**
     * @return ProbesCollection<int, MtrDotToolsProbeData>
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
     * @param ProbesCollection<int, MtrDotToolsProbeData> $probes
     * @return MtrResultsCollection<int, MtrDotToolsMtrResultsData>
     */
    public function mtr(
        array $ipData,
        ProbesCollection $probes,
        ?callable $intercept = null
    ): MtrResultsCollection {
        Assert::allIsInstanceOf($ipData, IpData::class);
        Assert::allIsInstanceOf($probes, MtrDotToolsProbeData::class);

        $requests = Collection::make($ipData)
            ->map(function (IpData $ipDataObject) use ($probes) {
                return Collection::make($probes)
                    ->map(fn (MtrDotToolsProbeData $probe) => ($this->performMtrRequest)($probe, $ipDataObject));
            })->flatten();

        $responses = Send::async($this->client, $requests->toArray());

        if ($intercept !== null) {
            $intercept($responses);
        }

        return MtrResultsCollection::make($responses)
            ->map(fn (RequestResponseWrapper $wrapper) => $this->performMtrMapper->map($wrapper, $probes));
    }

    /**
     * @param array<IpData> $ipData
     * @param ProbesCollection<int, MtrDotToolsProbeData> $probes
     * @return PingResultsCollection<int, MtrDotToolsPingResultsData>
     */
    public function ping(
        array $ipData,
        ProbesCollection $probes,
        ?callable $intercept = null
    ): PingResultsCollection {
        Assert::allIsInstanceOf($ipData, IpData::class);
        Assert::allIsInstanceOf($probes, MtrDotToolsProbeData::class);

        $requests = Collection::make($ipData)
            ->map(function (IpData $ipDataObject) use ($probes) {
                return Collection::make($probes)
                    ->map(fn (MtrDotToolsProbeData $probe) => ($this->performPingRequest)($probe, $ipDataObject));
            })->flatten();

        $responses = Send::async($this->client, $requests->toArray());

        if ($intercept !== null) {
            $intercept($responses);
        }

        return PingResultsCollection::make($responses)
            ->map(fn (RequestResponseWrapper $wrapper) => $this->performPingMapper->map($wrapper, $probes));
    }
}
