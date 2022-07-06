<?php

namespace XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Collections;

use Illuminate\Support\Collection;
use ArrayAccess;
use Illuminate\Support\Enumerable;

/**
 * @template TKey of array-key
 * @template TValue
 *
 * @implements ArrayAccess<TKey, TValue>
 * @implements Enumerable<TKey, TValue>
 */
class MtrResultsCollection extends Collection
{
    public function __construct(array $items = [])
    {
        parent::__construct($items);
    }
}