<?php

namespace XbNz\Resolver\Domain\Ip\Services\MtrDotTools\Enums;

enum TestType: string
{
    case MTR = 'mtr';
    case PING = 'ping';
}