<?php

declare(strict_types=1);

namespace XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Exceptions;

use GuzzleHttp\Exception\RequestException;

class MtrDotToolsException extends \Exception
{
    public static function fromRequestException(RequestException $exception): void
    {
        throw new self($exception->getMessage(), previous: $exception);
    }
}
