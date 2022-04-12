<?php

namespace XbNz\Resolver\Domain\Ip\Actions;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use XbNz\Resolver\Domain\Ip\DTOs\RekindledMtrDotShData;

class ConvertMtrPlainToJsonAction
{
    public function execute(RekindledMtrDotShData $rekindledData)
    {
        $exploded = collect(explode(PHP_EOL, $rekindledData->plainTextBody));

        $headers = collect(explode(' ', $exploded[0]))
            ->reject(fn($headerLine) => strlen($headerLine) < 1)
            ->values();

        unset($exploded[0]);

        $hops = $exploded
            ->reject(fn($row) => strlen($row) < 1)
            ->map(function ($row) use ($headers) {

                $targetInfo = [];

                $statistics = collect(explode(' ', $row))
                    ->reject(fn($rowValue) => strlen($rowValue) < 1)
                    ->values()
                    ->slice(1)
                    ->tap(function ($rowWithoutMtrStepIndex) use (&$targetInfo) {
                        $targetInfo = $rowWithoutMtrStepIndex
                            ->reject(fn($rowValue) => is_numeric($rowValue) || Str::contains($rowValue, '%'));
                    })
                    ->filter(fn($rowValue) => is_numeric($rowValue) || Str::contains($rowValue, '%'))
                    ->zip($headers)
                    ->reduce(function ($assoc, $keyValuePair) {
                        [$value, $key] = $keyValuePair;
                        $assoc[$key] = $value;
                        return $assoc;
                    });

                return [
                    'hop_host' => implode(' | ', $targetInfo->toArray()),
                    'statistics' => $statistics,
                ];
            });

        return Collection::make([
            'probe_id' => $rekindledData->probeId,
            'target_ip' => $rekindledData->ip,
            'hops' => $hops,
        ])->toJson();
    }
}