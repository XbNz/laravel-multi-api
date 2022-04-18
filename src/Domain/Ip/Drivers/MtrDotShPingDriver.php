<?php

declare(strict_types=1);

namespace XbNz\Resolver\Domain\Ip\Drivers;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Psr\Http\Message\RequestInterface;
use Webmozart\Assert\Assert;
use XbNz\Resolver\Domain\Ip\Actions\MtrProbeSearchAction;
use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Domain\Ip\Exceptions\MtrProbeNotFoundException;
use XbNz\Resolver\Support\Drivers\Driver;

class MtrDotShPingDriver implements Driver
{
    public const API_URL = 'https://mtr.sh/';

    public function __construct(
        private MtrProbeSearchAction $probeSearchAction
    ) {
    }

    /**
     * @return Collection<RequestInterface>
     * @throws MtrProbeNotFoundException
     */
    public function getRequests(array $dataObjects): Collection
    {
        Assert::allIsInstanceOf($dataObjects, IpData::class, '$dataObjects must be an array of IpData objects');
        $self = __CLASS__;

        $probes = Collection::make(Config::get("ip-resolver.{$self}.search"))
            ->map(fn (mixed $searchTerm) => $this->probeSearchAction->execute(isOnline: true, searchTerm: $searchTerm))
            ->flatten();

        $generator = static function (array $ipDataObjects) use ($probes) {
            foreach ($ipDataObjects as $ipData) {
                foreach ($probes as $probe) {
                    $supports = "supportsVersion{$ipData->version}";

                    if (! $probe->{$supports}) {
                        continue;
                    }

                    if (! $probe->canPerformPing) {
                        continue;
                    }

                    $uri = (new Uri(self::API_URL))
                        ->withPath("/{$probe->probeId}/ping/{$ipData->ip}");

                    yield new Request('GET', $uri);
                }
            }
        };

        $collectionOfRequests = new Collection(iterator_to_array($generator($dataObjects)));

        if ($collectionOfRequests->isEmpty()) {
            throw new MtrProbeNotFoundException('The MTR driver found no compatible probes, modify your search terms');
        }

        return new Collection(iterator_to_array($generator($dataObjects)));
    }

    public function supports(string $driver): bool
    {
        return $driver === __CLASS__;
    }
}
