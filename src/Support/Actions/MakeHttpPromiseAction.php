<?php

declare(strict_types=1);

namespace XbNz\Resolver\Support\Actions;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use XbNz\Resolver\Support\Drivers\Driver;
use XbNz\Resolver\Support\DTOs\GuzzleConfigData;
use XbNz\Resolver\Support\Exceptions\ApiProviderException;

class MakeHttpPromiseAction
{

    public function execute(GuzzleConfigData $config): PromiseInterface
    {
        $stack = HandlerStack::create(new CurlHandler());


        Collection::make($config->middlewares)
            ->each(function (callable $middleware) use (&$stack) {
                $stack->push($middleware);
            });

        $client = new Client([
            'handler' => $stack,
        ]);

        return $client->sendAsync(new Request('GET', 'http://testing.com'));

    }




}
