<?php

declare(strict_types=1);

namespace XbNz\Resolver\Domain\Ip\Actions;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use XbNz\Resolver\Domain\Ip\DTOs\MtrDotSh\MtrDotShProbeData;
use XbNz\Resolver\Factories\Ip\MtrDotShProbeFactory;

class MtrProbeSearchAction
{
    /**
     * @return Collection<MtrDotShProbeData>
     */
    public function execute(
        ?bool $ipv4 = null,
        ?bool $ipv6 = null,
        ?bool $isOnline = null,
        string $searchTerm = '*'
    ): Collection {
        $allProbesRaw = Cache::remember(
            'mtr_probes',
            Carbon::now()->addseconds(Config::get('resolver.cache_period')),
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
                function (Collection $collection) use ($searchTerm) {
                    return $collection->reject(function (MtrDotShProbeData $probe) use ($searchTerm) {
                        return Collection::make($probe)->filter(function ($value) use ($searchTerm) {
                            if (is_bool($value)) {
                                return false;
                            }

                            return Str::of($searchTerm)->lower()->contains(Str::of($value)->lower()->value());
                        })->isEmpty();
                    });
                }
            )

            ->values();
    }
}
