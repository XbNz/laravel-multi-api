<?php

declare(strict_types=1);

namespace XbNz\Resolver\Domain\Ip\Services\IpGeolocationDotIo\Collections;

use ArrayAccess;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
use XbNz\Resolver\Domain\Ip\Services\IpGeolocationDotIo\DTOs\IpGeolocationResultData;

/**
 * @template TKey of array-key
 * @template TValue
 *
 * @implements ArrayAccess<TKey, TValue>
 * @implements Enumerable<TKey, TValue>
 */
class IpGeolocationResultsCollection extends Collection
{
    /**
     * @param array<int, IpGeolocationResultData> $items
     */
    public function __construct(array $items = [])
    {
        parent::__construct($items);
    }
}
