<?php

declare(strict_types=1);

namespace XbNz\Resolver\Domain\Ip\Services\AbstractApiDotCom\Collections;

use ArrayAccess;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
use XbNz\Resolver\Domain\Ip\Services\AbstractApiDotCom\DTOs\AbstractApiGeolocationResultsData;

/**
 * @template TKey of array-key
 * @template TValue
 *
 * @implements ArrayAccess<TKey, TValue>
 * @implements Enumerable<TKey, TValue>
 */
class AbstractApiGeolocationResultsCollection extends Collection
{
    /**
     * @param array<int, AbstractApiGeolocationResultsData> $items
     */
    public function __construct(array $items = [])
    {
        parent::__construct($items);
    }
}
