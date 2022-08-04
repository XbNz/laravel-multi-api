<?php

use XbNz\Resolver\Domain\Ip\Services\AbstractApiDotCom\AbstractApiDotComService;
use XbNz\Resolver\Domain\Ip\Services\IpGeolocationDotIo\IpGeolocationDotIoService;

return [

    'api-keys' => [
        IpGeolocationDotIoService::class => [
            env('IP_GEOLOCATION_DOT_IO_API_KEY'),
        ],

        AbstractApiDotComService::class => [
            env('ABSTRACTAPI_DOT_COM_GEOLOCATION_API_KEY'),
        ],
    ],

];