<?php

namespace XbNz\Resolver\Factories;

use GuzzleHttp\Utils;
use Psr\Http\Message\ResponseInterface;
use XbNz\Resolver\Domain\Ip\DTOs\RawIpResultsData;

class RawIpResultsDataFactory
{
    /**
     * @throws \JsonException
     */
    public static function fromResponse(ResponseInterface $response, string $provider): RawIpResultsData
    {
        return new RawIpResultsData(
            $provider,
            Utils::jsonDecode($response->getBody()->getContents(), true, options: JSON_THROW_ON_ERROR)
        );
    }
}