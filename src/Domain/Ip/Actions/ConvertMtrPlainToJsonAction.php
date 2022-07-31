<?php

declare(strict_types=1);

namespace XbNz\Resolver\Domain\Ip\Actions;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Webmozart\Assert\Assert;
use XbNz\Resolver\Domain\Ip\DTOs\MtrDotSh\RekindledMtrData;
use XbNz\Resolver\Domain\Ip\Exceptions\ParseException;

class ConvertMtrPlainToJsonAction
{
    public function execute(RekindledMtrData $rekindledData): string
    {
        $exploded = explode(PHP_EOL, $rekindledData->plainTextBody);

        Assert::keyExists($exploded, 0);

        $headers = Collection::make(explode(' ', $exploded[0]))
            ->reject(fn ($headerLine) => strlen($headerLine) < 1)
            ->values();

        unset($exploded[0]);

        $hops = Collection::make($exploded)
            ->reject(fn ($row) => strlen($row) < 1)
            ->map(function ($row) use ($headers) {
                $targetInfo = [];

                $statistics = collect(explode(' ', $row))
                    ->reject(fn ($rowValue) => strlen($rowValue) < 1)
                    ->values()
                    ->slice(1)
                    ->tap(function (Collection $rowWithoutMtrStepIndex) use (&$targetInfo) {
                        $targetInfo = $rowWithoutMtrStepIndex
                            ->reject(fn ($rowValue) => is_numeric($rowValue) || Str::contains($rowValue, '%'));
                    })
                    ->filter(fn ($rowValue) => is_numeric($rowValue) || Str::contains($rowValue, '%'))
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

        if ($hops->isEmpty()) {
            throw new ParseException("Was not able to parse plain MTR response for probe {$rekindledData->probeId}");
        }

        return Collection::make([
            'probe_id' => $rekindledData->probeId,
            'target_ip' => $rekindledData->ip,
            'hops' => $hops,
        ])->toJson();
    }
}
