<?php

namespace XbNz\Resolver\Factories\Ip;

use XbNz\Resolver\Domain\Ip\DTOs\IpData;

class IpDataFactory
{
    public static function fromIp(string $ip): IpData
    {
        $v4 = filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_IPV4
        );

        $v6 = filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_IPV6
        );

        return match (true){
            is_string($v4) => new IpData(ip: $ip, version: 4),
            is_string($v6) => new IpData(ip: $ip, version: 6),
        };
    }

}