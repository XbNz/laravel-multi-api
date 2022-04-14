<?php

namespace XbNz\Resolver\Domain\Ip\Actions;

use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Domain\Ip\Exceptions\InvalidIpAddressException;
use XbNz\Resolver\Factories\Ip\IpDataFactory;


class VerifyIpIntegrityAction
{
    public function execute(string $ip): IpData | false
    {
        $validated = filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE|
            FILTER_FLAG_NO_RES_RANGE
        );

        // TODO: Remove this class, you have IpDataFactory and IpData doing lots of checks already

        if (! $validated){
            throw new InvalidIpAddressException(
                "The string {$ip} could not be validated as a public IPv4 or IPv6 address"
            );
        }

        return IpDataFactory::fromIp($validated);

    }
}