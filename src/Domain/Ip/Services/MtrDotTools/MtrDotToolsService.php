<?php

namespace XbNz\Resolver\Domain\Ip\Services\MtrDotTools;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Collection;
use JsonException;
use phpDocumentor\Reflection\DocBlock\Description;
use XbNz\Resolver\Domain\Ip\DTOs\MtrDotSh\MtrDotShProbeData;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Collections\ProbesCollection;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\DTOs\MtrDotToolsProbeData;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Enums\IpVersion;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Exceptions\MtrDotToolsException;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Requests\ListAllProbesRequest;
use XbNz\Resolver\Factories\Ip\MtrDotShProbeFactory;

class MtrDotToolsService
{
    public function __construct(
        private readonly Client $client,
        private readonly ListAllProbesRequest $listAllProbesRequest,
    ) {
    }


    /**
     * @return ProbesCollection<MtrDotToolsProbeData>
     * @throws MtrDotToolsException
     */
    public function listProbes(): ProbesCollection
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

        try {
            $jsonResponse = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new MtrDotToolsException('Failed to decode JSON response from MTR.tools');
        }


        return ProbesCollection::make($jsonResponse)
            ->map(fn (array $rawProbe, string $probeId) => MtrDotToolsProbeData::fromRaw($rawProbe, $probeId))
            ->values();
    }
}