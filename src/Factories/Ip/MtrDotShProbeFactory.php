<?php

declare(strict_types=1);

namespace XbNz\Resolver\Factories\Ip;

use XbNz\Resolver\Domain\Ip\DTOs\MtrDotSh\MtrDotShProbeData;

class MtrDotShProbeFactory
{
    /**
     * @param array<mixed> $raw
     */
    public static function fromRaw(string $probeId, array $raw): MtrDotShProbeData
    {

        return new MtrDotShProbeData(
            $probeId,
            (int) $raw['asnumber'],
            $raw['city'],
            $raw['country'],
            $raw['group'],
            $raw['provider'],
            $raw['providerurl'],
            $raw['unlocode'],
            $raw['caps']['mtr'] ?? false,
            $raw['caps']['dnst'] ?? false,
            $raw['caps']['trace'] ?? false,
            $raw['caps']['dnsr'] ?? false,
            $raw['caps']['ping'] ?? false,
            $raw['status'],
            $raw['residential'],
            $raw['status4'],
            $raw['status6'],
        );
    }
}
