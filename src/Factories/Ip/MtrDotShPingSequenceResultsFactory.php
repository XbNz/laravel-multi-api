<?php

declare(strict_types=1);

namespace XbNz\Resolver\Factories\Ip;

use XbNz\Resolver\Domain\Ip\DTOs\MtrDotSh\MtrDotShPingSequenceResultsData;

class MtrDotShPingSequenceResultsFactory
{
    /**
     * @param array<mixed> $sequence
     */
    public static function fromRawSequence(array $sequence, int $positionKey): MtrDotShPingSequenceResultsData
    {
        return new MtrDotShPingSequenceResultsData(
            (int) $sequence['size'],
            (string) $sequence['ip'],
            $positionKey,
            (int) $sequence['time_to_live'],
            (float) $sequence['rtt'],
        );
    }
}