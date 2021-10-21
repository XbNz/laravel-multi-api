<?php

return [
    'use_proxy' => false,
    'proxies' => [
        'https://1.1.1.1:49204'
    ],
    'timeout' => 1,
    'cache_period' => 3600,

    'use_retries' => true,
    'tries' => 2,
    'retry_sleep' => 5
];