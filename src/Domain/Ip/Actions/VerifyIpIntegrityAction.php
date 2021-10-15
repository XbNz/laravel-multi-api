<?php

namespace XbNz\Resolver\Domain\Ip\Actions;

use Illuminate\Pipeline\Pipeline;
use XbNz\Resolver\Domain\Ip\DTOs\IpData;

class VerifyIpIntegrityAction
{
    public function execute(string $ip): IpData | false
    {
        app(Pipeline::class)
            ->send($ip)
            ->through([

            ]);
    }
}