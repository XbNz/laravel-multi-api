<?php

namespace XbNz\Resolver\Domain\Ip\DTOs;

use Spatie\DataTransferObject\DataTransferObject;

class IpData extends DataTransferObject
{
    public string $ip;
    public int $version;
}