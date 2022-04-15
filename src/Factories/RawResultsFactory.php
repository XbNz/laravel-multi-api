<?php

declare(strict_types=1);

namespace XbNz\Resolver\Factories;

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
            json_decode((string) $response->getBody(), true, flags: JSON_THROW_ON_ERROR)
        );
    }
}
