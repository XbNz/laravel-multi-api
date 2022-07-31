<?php

declare(strict_types=1);

namespace XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Enums;

enum IpVersion: int
{
    case FOUR = 4;
    case SIX = 6;
}
