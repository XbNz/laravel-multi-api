<?php

declare(strict_types=1);

namespace XbNz\Resolver\Factories\Ip;

use Illuminate\Support\Str;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use XbNz\Resolver\Domain\Ip\DTOs\RekindledMtrDotShData;

class RekindledMtrDotShFactory
{
    public static function fromResponseAndRequest(
        ResponseInterface $response,
        RequestInterface $request
    ): RekindledMtrDotShData {
        [, $probeId, , $ip] = explode('/', $request->getUri()->getPath());

        return new RekindledMtrDotShData(
            (string) $response->getBody(),
            $probeId,
            $ip
        );
    }

    public static function generateTestData($overrides = []): RekindledMtrDotShData
    {
        $data = array_merge([
            'plain_text' => '                                                       Loss% Drop   Rcv   Snt  Last  Best   Avg  Wrst StDev Gmean Jttr Javg Jmax Jint
  1.|-- po-25.lag.iad03.us.misaka.io (23.143.176.4)    30.0%    3     7    10   0.2   0.1   0.2   0.2   0.0   0.2  0.0  0.0  0.1  0.2
  2.|-- v204.cr01.iad03.us.misaka.io (23.143.176.2)     0.0%    0    10    10   0.3   0.2   0.3   0.3   0.0   0.3  0.0  0.0  0.1  0.3
  3.|-- v204.cr02.iad03.us.misaka.io (23.143.176.3)     0.0%    0    10    10   0.3   0.2   0.3   0.4   0.1   0.3  0.0  0.1  0.1  0.4
  4.|-- e2-2.cr01.iad01.us.misaka.io (23.143.176.44)    0.0%    0    10    10   0.5   0.5   0.6   0.7   0.0   0.6  0.1  0.1  0.1  0.4
  5.|-- equinix-ashburn.woodynet.net (206.126.236.111)  0.0%    0    10    10   0.9   0.9   2.3   9.3   2.7   1.6  0.4  2.6  8.3 18.4
  6.|-- dns9.quad9.net (9.9.9.9)                        0.0%    0    10    10   0.6   0.6   0.7   0.7   0.0   0.7  0.0  0.0  0.1  0.3',

            'probe_id' => Str::random(5),
            'ip' => '1.1.1.1',
        ], $overrides);

        return new RekindledMtrDotShData(
            $data['plain_text'],
            $data['probe_id'],
            $data['ip']
        );
    }
}
