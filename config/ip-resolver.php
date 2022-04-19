<?php

return [

    'api-keys' => [
        \XbNz\Resolver\Domain\Ip\Drivers\IpApiDotComDriver::class => [
            '', '', ''
        ],

        \XbNz\Resolver\Domain\Ip\Drivers\IpGeolocationDotIoDriver::class => [
            '', '', ''
        ],

        \XbNz\Resolver\Domain\Ip\Drivers\IpInfoDotIoDriver::class => [
            '', '', ''
        ],

        \XbNz\Resolver\Domain\Ip\Drivers\IpDashApiDotComDriver::class => [
            '', '', ''
        ],

        \XbNz\Resolver\Domain\Ip\Drivers\AbuseIpDbDotComDriver::class => [
            '', '', ''
        ],

        \XbNz\Resolver\Domain\Ip\Drivers\AbstractApiDotComDriver::class => [
            '', '', ''
        ],
    ],

    /**
     * Visit https://mtr.sh/probes.json to retrieve the list of probe IDs
     */
    \XbNz\Resolver\Domain\Ip\Drivers\MtrDotShMtrDriver::class => [
        'search' => ['germany']
    ],

    \XbNz\Resolver\Domain\Ip\Drivers\MtrDotShPingDriver::class => [
        'search' => ['germany']
    ],
];