<?php

namespace XbNz\Resolver\Support\Actions;

use XbNz\Resolver\Support\Exceptions\ProxyNotValidException;

class GetRandomProxyAction
{
    public function execute(): string
    {
        $proxies = config('resolver.proxies');
        $validated = [];
        foreach ($proxies as $proxy) {
            $proxy = filter_var($proxy, FILTER_VALIDATE_URL);

            if (! $proxy){
                throw new ProxyNotValidException("The provided proxy: {$proxy} is not a valid structure");
            }

            $validated[] = $proxy;
        }

        //TODO: Refactor to "GetValidProxies" action and do Arr::random on calling code as needed.
        return \Arr::random($validated);
    }
}