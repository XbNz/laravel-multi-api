<?php

namespace XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Collections;

use ArrayAccess;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\DTOs\MtrDotToolsPingResultsData;

/**
 * @template TKey of array-key
 * @template TValue
 *
 * @implements ArrayAccess<TKey, TValue>
 * @implements Enumerable<TKey, TValue>
 */
class PingResultsCollection extends Collection
{
    public function __construct(array $items)
    {
        parent::__construct($items);
    }
}