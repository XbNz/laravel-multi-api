<?php

declare(strict_types=1);

namespace XbNz\Resolver\Support\Actions;

use Exception;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Illuminate\Support\Facades\Config;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class UniversalMiddlewaresAction
{
    private array $middlewares;

    public function __construct(
        private GetRandomProxyAction $randomProxy,
    ) {}

    // TODO: Refactor closed based middlewares to their own invokable classes
    public function execute(HandlerStack $stack): HandlerStack
    {
        if ($this->usingRetries()) {
            $stack->push($this->addRetries());
        }

        if ($this->usingProxy()) {
            $stack->push($this->addRandomProxy());
        }

        $stack->push($this->addTimeout());

        return $stack;
    }

    private function usingProxy(): bool
    {
        return (bool) Config::get('resolver.use_proxy');
    }

    private function usingRetries(): bool
    {
        return (bool) Config::get('resolver.use_retries');
    }

    private function addRetries(): callable
    {
        return Middleware::retry($this->retryDecider(), $this->retryDelay());
    }


    private function addTimeout(): callable
    {
        return static function (callable $handler) {
            return static function (RequestInterface $request, array $options) use ($handler) {

                $timeout = match (is_numeric(Config::get('resolver.timeout'))) {
                    true => Config::get('resolver.timeout'),
                    false => 5,
                };

                $options[ 'timeout' ] = (int) $timeout;

                return $handler($request, $options);

            };
        };
    }

    private function addRandomProxy(): callable
    {
        return function (callable $handler) {
            return function (RequestInterface $request, array $options) use ($handler) {
                $options[ 'proxy' ] = $this->randomProxy->execute();
                return $handler($request, $options);
            };
        };
    }

    private function retryDecider(): callable
    {
        return static function (
            int $retries,
            RequestInterface $request,
            ResponseInterface $response = null,
            Exception $exception = null
        ): bool {
            if ($retries >= Config::get('resolver.tries')) {
                return false;
            }

            if ($exception instanceof ConnectException) {
                return true;
            }

            if ($response && $response->getStatusCode() >= 400) {
                return true;
            }

            return false;
        };
    }

    private function retryDelay(): callable
    {
        $numberOfRetries = Config::get('resolver.tries');

        return static function () use ($numberOfRetries) {
            if (Config::has('resolver.retry_sleep')) {
                return (int) Config::get('resolver.retry_sleep');
            }

            return 1000 * $numberOfRetries;
        };

        // TODO: Impl of exponential backoff may not work. Need to test.
    }


}