<?php

declare(strict_types=1);

namespace XbNz\Resolver\Factories\Ip;

use XbNz\Resolver\Domain\Ip\DTOs\MtrDotShMtrHopResultsData;

class MtrDotShMtrHopResultsFactory
{
    public static function fromRawHop(array $hop, int $positionKey): MtrDotShMtrHopResultsData
    {
        return new MtrDotShMtrHopResultsData(
            $positionKey,
            $hop['hop_host'],
            (float) $hop['statistics']['Loss%'],
            (int) $hop['statistics']['Drop'],
            (int) $hop['statistics']['Rcv'],
            (int) $hop['statistics']['Snt'],
            (float) $hop['statistics']['Last'],
            (float) $hop['statistics']['Best'],
            (float) $hop['statistics']['Avg'],
            (float) $hop['statistics']['Wrst'],
            (float) $hop['statistics']['StDev'],
            (float) $hop['statistics']['Gmean'],
            (float) $hop['statistics']['Jttr'],
            (float) $hop['statistics']['Javg'],
            (float) $hop['statistics']['Jmax'],
            (float) $hop['statistics']['Jint'],
        );
    }
}