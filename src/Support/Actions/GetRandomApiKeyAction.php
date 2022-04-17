<?php

declare(strict_types=1);

namespace XbNz\Resolver\Support\Actions;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ItemNotFoundException;
use XbNz\Resolver\Support\Exceptions\ConfigNotFoundException;
use XbNz\Resolver\Support\Exceptions\MissingApiKeyException;

class GetRandomApiKeyAction
{
    /**
     * @param string $dotNotatedRootConfigPath (e.g. '{configfilename}.{basekey}'). Should not include the name of service.
     * @throws ConfigNotFoundException
     * @throws MissingApiKeyException
     */
    public function execute(string $driver, string $dotNotatedRootConfigPath): string
    {
        try {
            $targetKeys = Collection::make(Config::get($dotNotatedRootConfigPath))
                ->sole(fn (array $providerKeys, string $providerName) => $driver === $providerName);

        } catch (ItemNotFoundException $e) {
            throw new ConfigNotFoundException("The given driver '{$driver}' is not configured in the config file.");
        }

        $keys = Collection::make($targetKeys)->reject(fn (string $apiKey) => is_string($apiKey) === false);

        if ($keys->isEmpty()) {
            throw new MissingApiKeyException("{$driver} does not have any API keys configured in the config file. Keys must be in string format.");
        }

        return $keys->random();
    }
}
