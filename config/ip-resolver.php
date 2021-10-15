<?php

return [
    'api-keys' => [
        'ipApi' => env('IPAPI_KEY'),
        'ipGeolocation' => env('IPGEOLOCATION_KEY'),
        'ipInfo' => env('IPINFO_KEY'),
    ],

    'files' => [
        'maxMind' => env('MAXMIND_LOCATION'),
        'ipDb' => env('IPDB_LOCATION')
    ]
];