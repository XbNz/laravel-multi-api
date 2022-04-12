<?php

namespace XbNz\Resolver\Support\Actions;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ItemNotFoundException;
use Illuminate\Support\Str;
use Psr\Http\Message\UriInterface;
use XbNz\Resolver\Support\Exceptions\ConfigNotFoundException;
use XbNz\Resolver\Support\Exceptions\MissingApiKeyException;

class GetRandomApiKeyAction
{
    /**
     * @param UriInterface $uri Must contain at least the base URI of the provider (e.g. https://api.example.com).
     * Key search will be non-resultant if the base URI is not present in config file
     *
     *
     * @param string $dotNotatedRootConfigPath (e.g. '{configfilename}.{basekey}'). Should not include the name of service.
     * @throws ConfigNotFoundException
     * @throws MissingApiKeyException
     */
    public function execute(string $driver, string $dotNotatedRootConfigPath): string
    {

        try {
            $targetKeys = Collection::make(Config::get($dotNotatedRootConfigPath))
                ->sole(fn (array|string $providerKeys, $providerName) => $driver === $providerName);
        } catch (ItemNotFoundException $e) {
            throw new ConfigNotFoundException("The given driver '{$driver}' is not configured in the config file.");
        }

        $keys = Collection::make($targetKeys);

        if ($keys->isEmpty()) {
            throw new MissingApiKeyException("{$driver} does not have any API keys configured in the config file.");
        }

        return Collection::make($targetKeys)
            ->random();
    }
}


