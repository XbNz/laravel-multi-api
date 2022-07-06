<?php

namespace XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Collections;

use ArrayAccess;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
use Illuminate\Support\Str;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\DTOs\MtrDotToolsProbeData;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Enums\IpVersion;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Enums\MTR;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Enums\Ping;
use XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Enums\TestType;
use XbNz\Resolver\Support\DTOs\Mappable;

/**
 * @template TKey of array-key
 * @template TValue
 *
 * @implements ArrayAccess<TKey, TValue>
 * @implements Enumerable<TKey, TValue>
 */
class ProbesCollection extends Collection
{
    public function __construct(array $items = [])
    {
        parent::__construct($items);
    }

    public function canPerformMtrOn(IpVersion $ipVersion): self
    {
        return $this->filter(fn(MtrDotToolsProbeData $data) => $data->mtr->compatibleWith($ipVersion));
    }

    public function canPerformPingOn(IpVersion $ipVersion): self
    {
        return $this->filter(fn(MtrDotToolsProbeData $data) => $data->ping->compatibleWith($ipVersion));
    }

    public function fuzzySearch(string $term): self
    {
        return $this->filter(static function (MtrDotToolsProbeData $data) use ($term) {
            return Collection::make($data)
                ->reject(fn(mixed $value) => is_string($value) === false)
                ->filter(fn(string $value) => Str::of($term)->lower()->contains((string) Str::of($value)->lower()))
                ->isNotEmpty();
        });
    }

    public function online(bool $online = true): self
    {
        return $this->filter(fn(MtrDotToolsProbeData $data) => $data->isOnline === $online);
    }
}