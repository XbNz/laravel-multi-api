<?php

namespace XbNz\Resolver\Support\Actions;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ItemNotFoundException;
use Illuminate\Support\Str;
use Psr\Http\Message\UriInterface;
use XbNz\Resolver\Support\Exceptions\ConfigNotFoundException;

class GetRandomApiKeyAction
{
    /**
     * @throws ConfigNotFoundException
     * @param UriInterface $uri Must contain at least the base URI of the provider (e.g. https://api.example.com).
     * Key search will be non-resultant if the base URI is not present in config file
     *
     *
     * @param string $dotNotatedRootConfigPath (e.g. '{configfilename}.{basekey}'). Should not include the name of service.
     */
    public function execute(UriInterface $uri, string $dotNotatedRootConfigPath): string
    {
        $host = Str::of($uri->getHost());

        try {
            $targetKeys = Collection::make(Config::get($dotNotatedRootConfigPath))
                ->sole(fn (array $providerKeys, $providerName) => $host->contains($providerName));
        } catch (ItemNotFoundException $e) {
            throw new ConfigNotFoundException("The given host '{$host}' is not configured in the config file.");
        }

        return Collection::make($targetKeys)
            ->random();
    }
}


