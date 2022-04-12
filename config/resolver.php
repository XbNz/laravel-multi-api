<?php

return [
    'use_proxy' => false,
    'proxies' => [],

    'timeout' => 20,
    'cache_period' => 3600,

    'async_concurrent_requests' => 100,

    'use_retries' => true,
    'tries' => 5,
    'retry_sleep' => 2,
    'retry_sleep_multiplier' => 1.5,
];