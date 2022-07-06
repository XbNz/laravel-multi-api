<?php

namespace XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Mappers;

use GuzzleHttp\Psr7\Response;
use JsonException;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Collections\ProbesCollection;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\DTOs\MtrDotToolsProbeData;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Exceptions\MtrDotToolsException;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Requests\ListAllProbes\ListAllProbesRequest;
use XbNz\Resolver\Domain\Ip\Services\Request;
use XbNz\Resolver\Support\DTOs\RequestResponseWrapper;
use XbNz\Resolver\Support\Mappings\Mapper;

class ListAllProbesMapper implements Mapper
{
    /**
     * @return ProbesCollection<MtrDotToolsProbeData>
     * @throws MtrDotToolsException
     */
    public static function map(RequestResponseWrapper $requestResponse): ProbesCollection
    {
        try {
            $jsonResponse = json_decode(
                $requestResponse->guzzleResponse->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR
            );
        } catch (JsonException $e) {
            throw new MtrDotToolsException('Failed to decode JSON response from MTR.tools');
        }

        return ProbesCollection::make($jsonResponse)
            ->map(fn (array $rawProbe, string $probeId) => MtrDotToolsProbeData::fromRaw($rawProbe, $probeId))
            ->values();
    }
}