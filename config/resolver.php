<?php

return [
    'use_proxy' => false,
    'proxies' => [],

    'timeout' => 3,
    'cache_period' => 3600,

    'async_concurrent_requests' => 10,

    'use_retries' => true,
    'tries' => 5,
    'retry_sleep' => 5,
    'retry_sleep_multiplier' => 2.0,
];