<?php

declare(strict_types=1);

namespace XbNz\Resolver\Support\Actions;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use XbNz\Resolver\Support\Exceptions\ProxyException;

class GetRandomProxyAction
{
    public function execute(): string
    {
        $proxies = Config::get('resolver.proxies');

        if (count($proxies) === 0) {
            throw new ProxyException('No proxies found in config.');
        }

        $validated = [];
        foreach ($proxies as $proxy) {
            if (! filter_var($proxy, FILTER_VALIDATE_URL)) {
                throw new ProxyException("The provided proxy: '{$proxy}' is not a valid structure");
            }

            $validated[] = $proxy;
        }

        return Arr::random($validated);
    }
}
