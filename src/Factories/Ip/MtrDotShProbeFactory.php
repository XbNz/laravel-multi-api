<?php

namespace XbNz\Resolver\Factories\Ip;

use XbNz\Resolver\Domain\Ip\DTOs\MtrDotShProbe;

class MtrDotShProbeFactory
{
    public static function fromRaw(string $probeId, array $raw): MtrDotShProbe
    {
        return new MtrDotShProbe(
            $probeId,
            $raw['asnumber'],
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