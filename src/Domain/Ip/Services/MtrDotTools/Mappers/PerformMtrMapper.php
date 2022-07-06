<?php

namespace XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Mappers;

use GuzzleHttp\Psr7\Response;
use JsonException;
use XbNz\Resolver\Domain\Ip\Actions\ConvertMtrPlainToJsonAction;
use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Domain\Ip\DTOs\MtrDotSh\MtrResultData;
use XbNz\Resolver\Domain\Ip\DTOs\MtrDotSh\RekindledMtrData;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Collections\HopCollection;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Collections\MtrResultsCollection;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Collections\ProbesCollection;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\DTOs\MtrDotToolsHopData;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\DTOs\MtrDotToolsMtrResultsData;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Exceptions\MtrDotToolsException;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\MtrDotToolsService;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Requests\PerformMtr\PerformMtrRequest;
use XbNz\Resolver\Factories\Ip\MtrDotShMtrHopResultsFactory;
use XbNz\Resolver\Factories\Ip\RekindledMtrDotShFactory;
use XbNz\Resolver\Support\DTOs\RequestResponseWrapper;
use XbNz\Resolver\Support\Mappings\Mapper;

class PerformMtrMapper
{
    public function __construct(
        private readonly ConvertMtrPlainToJsonAction $convertMtrPlainToJsonAction,
    ) {
    }

    /**
     * @throws MtrDotToolsException
     */
    public function map(RequestResponseWrapper $requestResponse, ProbesCollection $probesCollection): MtrDotToolsMtrResultsData
    {
        $plainTextResponse = $requestResponse->guzzleResponse->getBody()->getContents();

        [, $probeId, , $ip] = explode('/', $requestResponse->guzzleRequest->getUri()->getPath());
        $ipData = IpData::fromIp($ip);

        $probe = $probesCollection->findById($probeId);

        $json = $this->convertMtrPlainToJsonAction->execute(
            new RekindledMtrData($plainTextResponse, $probe->probeId, $ip),
        );

        $arrayOfMtrData = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        $hops = HopCollection::make($arrayOfMtrData['hops'])
            ->map(fn(array $hop, int $index) => MtrDotToolsHopData::fromRaw($hop, $index));

        return new MtrDotToolsMtrResultsData(
            $probe,
            $ipData,
            $hops,
        );
    }
}