<?php

namespace XbNz\Resolver\Support\Actions;

use Config;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use XbNz\Resolver\Support\ApiExceptionHandlers\Handler;
use XbNz\Resolver\Support\Drivers\Driver;
use XbNz\Resolver\Support\Exceptions\ApiProviderException;

class MakeHttpCallAction
{
    public function execute(string $url, Driver $driver, array $params = []): Response
    {
        $options = [
            'timeout' => config('resolver.timeout'),
        ];

        if ($this->usingProxy()){
            $options['proxy'] = \Arr::random(config('resolver.proxies'));
        }

        $response = retry(
            config('resolver.retry_times'),
            function () use ($url, $params, $options){
                return Http::withOptions($options)->get($url, $params);
            }, config('resolver.retry_sleep')
        );

        if (! $response->successful()){
            $message = "{$driver->supports()} has hit a snag and threw a {$response->status()} error" . PHP_EOL;
            throw new ApiProviderException($message);
        }

        return $response;
    }

    private function usingProxy(): bool
    {
        return Config::get('resolver.use_proxy') === true;
    }
}
