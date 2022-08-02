<?php

declare(strict_types=1);

namespace XbNz\Resolver\Domain\Ip\Services\IpGeolocationDotIo;

use GuzzleHttp\ClientInterface;
use Illuminate\Support\Collection;
use Webmozart\Assert\Assert;
use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Domain\Ip\Services\IpGeolocationDotIo\Requests\GeolocationRequest;
use XbNz\Resolver\Domain\Ip\Services\Service;
use XbNz\Resolver\Support\Helpers\Send;

class IpGeolocationDotIoService implements Service
{
    public function __construct(
        private readonly ClientInterface $client,
    ) {
    }

    /**
     * @param array<IpData> $ipData
     */
    public function geolocate(
        array $ipData,
        ?callable $intercept = null
    ) {
        Assert::allIsInstanceOf($ipData, IpData::class);

        $requests = Collection::make($ipData)
            ->map(function (IpData $ipDataObject) {
                return GeolocationRequest::generate($ipDataObject);
            });

        $responses = Send::async($this->client, $requests->toArray());

        if ($intercept !== null) {
            $intercept($responses);
        }

        dd($responses);
//        return
    }
}
