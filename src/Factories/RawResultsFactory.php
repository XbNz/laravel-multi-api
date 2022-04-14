<?php

namespace XbNz\Resolver\Factories;

use GuzzleHttp\Utils;
use Psr\Http\Message\ResponseInterface;
use XbNz\Resolver\Support\DTOs\RawResultsData;

class RawResultsFactory
{
    /**
     * @throws \JsonException
     */
    public static function fromResponse(ResponseInterface $response, string $driver): RawResultsData
    {
        return new RawResultsData(
            $driver,
            Utils::jsonDecode($response->getBody()->getContents(), true, options: JSON_THROW_ON_ERROR)
        );
    }
}