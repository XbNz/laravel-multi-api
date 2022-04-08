<?php

namespace XbNz\Resolver\Support\Actions;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use XbNz\Resolver\Support\Exceptions\ProxyNotValidException;

class GetRandomProxyAction
{
    public function execute(): string
    {
        $proxies = Config::get('resolver.proxies');
        $validated = [];
        foreach ($proxies as $proxy) {

            if (! filter_var($proxy, FILTER_VALIDATE_URL)){
                throw new ProxyNotValidException("The provided proxy: '{$proxy}' is not a valid structure");
            }

            $validated[] = $proxy;
        }

        return Arr::random($validated);
    }
}