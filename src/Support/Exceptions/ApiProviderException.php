<?php

declare(strict_types=1);

namespace XbNz\Resolver\Support\Exceptions;

use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\TransferException;

class ApiProviderException extends \Exception
{
    public const UNAUTHORIZED = 'Please check your credentials for this service in the corresponding config file';

    public const FORBIDDEN = 'Try accessing the url with a browser, maybe you are being blocked by a CDN or firewall';

    public const TIMEOUT = 'Increase your timeout value in the config file';

    public const RATELIMIT = 'You might be getting rate limited. Try configuring proxies or multiple API tokens in the config file';

    public const UNKNOWN = 'Check your connection or increase timeout. If you have set proxies, ensure they are alive';

    public static function fromTransferException(TransferException $e): self
    {
        if ($e instanceof BadResponseException) {
            throw match ($e->getResponse()->getStatusCode()) {
                401 => new self(
                    "{$e->getRequest()->getUri()} threw a {$e->getResponse()->getStatusCode()} error. Reason phrase: {$e->getResponse()->getReasonPhrase()}. " . self::UNAUTHORIZED,
                    previous: $e
                ),

                403 => new self(
                    "{$e->getRequest()->getUri()} threw a {$e->getResponse()->getStatusCode()} error. Reason phrase: {$e->getResponse()->getReasonPhrase()}. " . self::FORBIDDEN,
                    previous: $e
                ),

                408 => new self(
                    "{$e->getRequest()->getUri()} threw a {$e->getResponse()->getStatusCode()} error. Reason phrase: {$e->getResponse()->getReasonPhrase()}. " . self::TIMEOUT,
                    previous: $e
                ),

                429 => new self(
                    "{$e->getRequest()->getUri()} threw a {$e->getResponse()->getStatusCode()} error. Reason phrase: {$e->getResponse()->getReasonPhrase()}. " . self::RATELIMIT,
                    previous: $e
                ),

                default => new self(
                    "{$e->getRequest()->getUri()} threw a {$e->getResponse()->getStatusCode()} error. Reason phrase: {$e->getResponse()->getReasonPhrase()}. ",
                    previous: $e
                ),
            };
        }

        throw new self(
            $e->getMessage() . self::UNKNOWN,
            previous: $e
        );
    }
}
