<?php

declare(strict_types=1);

namespace XbNz\Resolver\Domain\Ip\Actions;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use XbNz\Resolver\Domain\Ip\DTOs\MtrDotShProbeData;
use XbNz\Resolver\Factories\Ip\MtrDotShProbeFactory;

class MtrProbeSearchAction
{
    /**
     * @return Collection<MtrDotShProbeData>
     */
    public function execute(
        ?bool  $ipv4 = null,
        ?bool  $ipv6 = null,
        ?bool  $isOnline = null,
        string $searchTerm = '*'
    ): Collection {
        $allProbesRaw = Cache::remember(
            'mtr_probes',
            now()->addseconds(Config::get('resolver.cache_period')),
            static function () {
                $client = Config::get('resolver.use_retries')
                    ? Http::retry(Config::get('resolver.tries'), Config::get('resolver.retry_sleep'))
                    : Http::withOptions([
                        'timeout' => Config::get('resolver.timeout'),
                    ]);

                return $client
                    ->timeout(Config::get('resolver.timeout'))
                    ->get('https://mtr.sh/probes.json')
                    ->throw()
                    ->json();
            }
        );

        $collection = Collection::make($allProbesRaw)
            ->map(fn ($rawProbe, $probeId) => MtrDotShProbeFactory::fromRaw($probeId, $rawProbe))
            ->values();

        return $collection
            ->when(
                $ipv4 !== null,
                fn (Collection $collection) => $collection->filter(
                    fn (MtrDotShProbeData $probe) => $probe->supportsVersion4 === $ipv4
                )
            )

            ->when(
                $ipv6 !== null,
                fn (Collection $collection) => $collection->filter(
                    fn (MtrDotShProbeData $probe) => $probe->supportsVersion6 === $ipv6
                )
            )

            ->when(
                $isOnline !== null,
                fn (Collection $collection) => $collection->filter(
                    fn (MtrDotShProbeData $probe) => $probe->isOnline === $isOnline
                )
            )

            ->when(
                $searchTerm !== '*',
                fn (Collection $collection) => $collection->reject(
                    fn (MtrDotShProbeData $probe) => Collection::make($probe)->filter(
                        fn ($value) => Str::of($searchTerm)->lower()->contains(Str::of($value)->lower())
                    )->isEmpty()
                )
            )

            ->values();
    }
}
