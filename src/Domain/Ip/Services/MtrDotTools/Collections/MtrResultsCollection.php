<?php

declare(strict_types=1);

namespace XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Collections;

use ArrayAccess;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\DTOs\MtrDotToolsMtrResultsData;

/**
 * @template TKey of array-key
 * @template TValue
 *
 * @implements ArrayAccess<TKey, TValue>
 * @implements Enumerable<TKey, TValue>
 */
class MtrResultsCollection extends Collection
{
    /**
     * @param array<int, MtrDotToolsMtrResultsData> $items
     */
    public function __construct(array $items = [])
    {
        parent::__construct($items);
    }
}
