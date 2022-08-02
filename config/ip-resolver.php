<?php

use XbNz\Resolver\Domain\Ip\Services\IpGeolocationDotIo\IpGeolocationDotIoService;

return [

    'api-keys' => [
//        \XbNz\Resolver\Domain\Ip\Services\MtrDotTools\MtrDotToolsService::class => [
//            '', '', ''
//        ],

        IpGeolocationDotIoService::class => [
            env('IP_GEOLOCATION_DOT_IO_API_KEY'),
        ],
    ],

];