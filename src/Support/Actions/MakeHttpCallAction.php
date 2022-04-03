<?php

namespace XbNz\Resolver\Support\Actions;

use Illuminate\Support\Facades\Config;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
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
            $response = tap(Http::withOptions($options), function ($client) use ($url, $params){
                if (! $this->usingRetries()){
                    return $client;
                }
                return $client->retry(config('resolver.tries'), config('resolver.retry_sleep'));
            })
                ->get($url, $params)
                ->throw();

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

    private function usingRetries(): bool
    {
        return Config::get('resolver.use_retries') === true;
    }
}
