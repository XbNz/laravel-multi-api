<?php

namespace XbNz\Resolver\Domain\Ip\Actions;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use XbNz\Resolver\Domain\Ip\DTOs\MtrDotShProbe;
use XbNz\Resolver\Factories\Ip\MtrDotShProbeFactory;

class MtrProbeSearchAction
{
    /**
     * @return Collection<MtrDotShProbe>
     */
    public function execute(
        ?bool $v4 = null,
        ?bool $v6 = null,
        ?bool $isOnline = null,
        string $searchTerm = '*'
    ): Collection
    {
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
                ! is_null($v4),
                fn (Collection $collection) => $collection->filter(
                    fn (MtrDotShProbe $probe) => $probe->supportsVersion4 === $v4
                )
            )

            ->when(
                ! is_null($v6),
                fn (Collection $collection) => $collection->filter(
                    fn (MtrDotShProbe $probe) => $probe->supportsVersion6 === $v6
                )
            )

            ->when(
                ! is_null($isOnline),
                fn (Collection $collection) => $collection->filter(
                    fn (MtrDotShProbe $probe) => $probe->isOnline === $isOnline
                )
            )

            ->when(
                $searchTerm !== '*',
                fn (Collection $collection) => $collection->reject(
                    fn (MtrDotShProbe $probe) => Collection::make($probe)->filter(
                        fn ($value) => Str::of($searchTerm)->lower()->contains(Str::of($value)->lower())
                    )->isEmpty()
                )
            )

            ->values();
    }
}

