<?php

namespace XbNz\Resolver\Factories\Ip;

use GuzzleHttp\Utils;
use Psr\Http\Message\ResponseInterface;
use XbNz\Resolver\Domain\Ip\DTOs\RawIpResultsData;

class RawIpResultsDataFactory
{
    /**
     * @throws \JsonException
     */
    public static function fromResponse(ResponseInterface $response, string $driver): RawIpResultsData
    {
        return new RawIpResultsData(
            $driver,
            Utils::jsonDecode($response->getBody()->getContents(), true, options: JSON_THROW_ON_ERROR)
        );
    }
}