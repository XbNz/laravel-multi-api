<?php

namespace XbNz\Resolver\Domain\Ip\Drivers;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Webmozart\Assert\Assert;
use XbNz\Resolver\Domain\Ip\Actions\MtrProbeSearchAction;
use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Support\Drivers\Driver;

class MtrDotShMtrDriver implements Driver
{
    const API_URL = 'https://mtr.sh/';

    public function __construct(
        private MtrProbeSearchAction $probeSearchAction
    )
    {}


    public function getRequests(array $ipDataObjects): Collection
    {
        Assert::allIsInstanceOf($ipDataObjects, IpData::class, '$ipDataObjects must be an array of IpData objects');
        $self = __CLASS__;

        $probes = Collection::make(Config::get("ip-resolver.{$self}.search"))
            ->map(fn (mixed $searchTerm) => $this->probeSearchAction->execute(isOnline: true, searchTerm: $searchTerm))
            ->flatten();

        $generator = static function (array $ipDataObjects) use ($probes) {
            foreach ($ipDataObjects as $ipData) {
                foreach ($probes as $probe) {
                    $supports = "supportsVersion{$ipData->version}";
                    if (! $probe->$supports) {
                        continue;
                    }

                    $uri = (new Uri(self::API_URL))
                        ->withPath("/{$probe->probeId}/mtr/{$ipData->ip}");
                    yield new Request('GET', $uri);
                }
            }
        };

        return new Collection(iterator_to_array($generator($ipDataObjects)));
    }

    public function supports(string $driver): bool
    {
        return $driver === __CLASS__;
    }
}