<?php

namespace XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Mappers;

use GuzzleHttp\Psr7\Response;
use JsonException;
use XbNz\Resolver\Domain\Ip\DTOs\MtrDotSh\MtrResultData;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Collections\MtrResultsCollection;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Exceptions\MtrDotToolsException;
use XbNz\Resolver\Factories\Ip\RekindledMtrDotShFactory;
use XbNz\Resolver\Support\DTOs\RequestResponseWrapper;
use XbNz\Resolver\Support\Mappings\Mapper;

class PerformMtrMapper implements Mapper
{
    public function __construct(

    ) {
    }

    /**
     * @throws MtrDotToolsException
     */
    public static function map(RequestResponseWrapper $requestResponse): MtrResultData
    {
        try {
            $jsonResponse = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new MtrDotToolsException('Failed to decode JSON response from MTR.tools');
        }

        return new MtrResultData(
            // TODO: this
        )

    }
}