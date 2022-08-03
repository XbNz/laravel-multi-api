<?php

declare(strict_types=1);

namespace XbNz\Resolver\Domain\Ip\Services\IpGeolocationDotIo;

use GuzzleHttp\ClientInterface;
use Illuminate\Support\Collection;
use Webmozart\Assert\Assert;
use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Domain\Ip\Services\IpGeolocationDotIo\Collections\IpGeolocationResultsCollection;
use XbNz\Resolver\Domain\Ip\Services\IpGeolocationDotIo\DTOs\IpGeolocationResultData;
use XbNz\Resolver\Domain\Ip\Services\IpGeolocationDotIo\Mappers\GeolocationMapper;
use XbNz\Resolver\Domain\Ip\Services\IpGeolocationDotIo\Requests\GeolocationRequest;
use XbNz\Resolver\Domain\Ip\Services\Service;
use XbNz\Resolver\Support\DTOs\RequestResponseWrapper;
use XbNz\Resolver\Support\Helpers\Send;

class IpGeolocationDotIoService implements Service
{
    public function __construct(
        private readonly ClientInterface $client,
        private readonly GeolocationMapper $geolocationMapper,
    ) {
    }

    /**
     * @param array<IpData> $ipData
     * @return IpGeolocationResultsCollection<int, IpGeolocationResultData>
     */
    public function geolocate(
        array $ipData,
        ?callable $intercept = null
    ): IpGeolocationResultsCollection {
        Assert::allIsInstanceOf($ipData, IpData::class);

        $requests = Collection::make($ipData)
            ->map(function (IpData $ipDataObject) {
                return GeolocationRequest::generate($ipDataObject);
            });

        $responses = Send::async($this->client, $requests->toArray());

        if ($intercept !== null) {
            $intercept($responses);
        }

        return IpGeolocationResultsCollection::make($responses)
            ->map(fn (RequestResponseWrapper $wrapper) => $this->geolocationMapper->map($wrapper));
    }
}
