<?php

namespace XbNz\Resolver\Support\Actions;

use Config;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use XbNz\Resolver\Support\ApiExceptionHandlers\Handler;
use XbNz\Resolver\Support\Drivers\Driver;
use XbNz\Resolver\Support\Exceptions\ApiProviderException;

class MakeHttpCallAction
{
    public function __construct(private GetRandomProxyAction $randomProxy)
    {}

    public function execute(string $url, Driver $driver, array $params = []): Response
    {
        $options = [
            'timeout' => config('resolver.timeout'),
        ];

        if ($this->usingProxy()){
            $options['proxy'] = $this->randomProxy->execute();
        }

        try {
            $response = Http::retry(config('resolver.retry_times'), config('resolver.retry_sleep'))
                ->withOptions($options)
                ->get($url, $params);
        } catch (RequestException $e) {
            $message = "{$driver->supports()} has hit a snag and threw a {$e->response->status()} error" . PHP_EOL;
            throw new ApiProviderException($message);
        }

        return $response;
    }

    private function usingProxy(): bool
    {
        return Config::get('resolver.use_proxy') === true;
    }
}
