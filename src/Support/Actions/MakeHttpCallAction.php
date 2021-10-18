<?php

namespace XbNz\Resolver\Support\Actions;

use Config;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use XbNz\Resolver\Domain\Ip\Drivers\Driver;

class MakeHttpCallAction
{
    public function execute(string $url, array $params = []): Response
    {
        $options = [
            'timeout' => config('resolver.timeout'),
        ];

        if ($this->usingProxy()){
            $options['proxy'] = \Arr::random(config('resolver.proxies'));
        }

        return Http::withOptions($options)->get($url, $params);
    }

    private function usingProxy(): bool
    {
        return Config::get('resolver.use_proxy') === true;
    }
}
