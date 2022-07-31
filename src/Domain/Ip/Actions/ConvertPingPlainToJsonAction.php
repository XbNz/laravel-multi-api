<?php

declare(strict_types=1);

namespace XbNz\Resolver\Domain\Ip\Actions;

use Illuminate\Support\ItemNotFoundException;
use Illuminate\Support\Str;
use XbNz\Resolver\Domain\Ip\DTOs\MtrDotSh\RekindledMtrData;
use XbNz\Resolver\Domain\Ip\Exceptions\ParseException;

class ConvertPingPlainToJsonAction
{
    /**
     * @throws ParseException
     */
    public function execute(RekindledMtrData $rekindledData): string
    {
        $plain = $rekindledData->plainTextBody;

        $explodedByLine = collect(explode(PHP_EOL, $plain));

        try {
            $packetsTransmittedLine = $explodedByLine->sole(fn (string $line) => Str::of($line)->contains('packets transmitted'));
        } catch (ItemNotFoundException $e) {
            throw new ParseException("Was not able to parse plain ping response for probe {$rekindledData->probeId}");
        }

        $sequences = $explodedByLine
            ->filter(fn (string $line) => Str::of($line)->contains('icmp_seq'))
            ->values()
            ->map(function (string $sequence) {
                return [
                    'size' => (int) Str::of($sequence)->before('bytes from')->trim()->value(),
                    'ip' => Str::of($sequence)->between('from', ':')->trim()->value(),
                    'sequence_number' => (int) Str::of($sequence)->between('q=', 'ttl')->trim()->value(),
                    'time_to_live' => (int) Str::of($sequence)->between('l=', 'time')->trim()->value(),
                    'rtt' => (float) Str::of($sequence)->between('e=', 'ms')->trim()->value(),
                ];
            });

        $packetsTransmitted = (int) Str::of($packetsTransmittedLine)
            ->before('packets transmitted')
            ->value();

        $packetLoss = 100 - (count($sequences) / $packetsTransmitted * 100);

        if ($sequences->isNotEmpty()) {
            $rttValues = $sequences->pluck('rtt');

            $rttStatistics = [
                'minimum_rtt' => $sequences->min('rtt'),
                'average_rtt' => $sequences->avg('rtt'),
                'maximum_rtt' => $sequences->max('rtt'),
                'jitter' => $rttValues
                    ->filter(fn ($item, int $index) => $rttValues->has($index + 1))
                    ->map(fn (float $item, int $index) => abs($item - $rttValues[$index + 1]))
                    ->avg(),
            ];
        }

        return json_encode([
            'probe_id' => $rekindledData->probeId,
            'target_ip' => $rekindledData->ip,
            'sequences' => $sequences->isEmpty() ? null : $sequences->toArray(),
            'packet_loss' => $packetLoss,
            'statistics' => $rttStatistics ?? null,
        ], JSON_THROW_ON_ERROR);
    }
}
