<?php

declare(strict_types=1);

namespace XbNz\Resolver\Domain\Ip\Services\AbstractApiDotCom;

use GuzzleHttp\ClientInterface;
use Illuminate\Support\Collection;
use Webmozart\Assert\Assert;
use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Domain\Ip\Services\AbstractApiDotCom\Collections\AbstractApiGeolocationResultsCollection;
use XbNz\Resolver\Domain\Ip\Services\AbstractApiDotCom\DTOs\AbstractApiGeolocationResultsData;
use XbNz\Resolver\Domain\Ip\Services\AbstractApiDotCom\Mappers\GeolocationMapper;
use XbNz\Resolver\Domain\Ip\Services\AbstractApiDotCom\Requests\GeolocationRequest;
use XbNz\Resolver\Support\DTOs\RequestResponseWrapper;
use XbNz\Resolver\Support\Helpers\Send;

class AbstractApiDotComService
{
    public function __construct(
        private readonly ClientInterface $client,
        private readonly GeolocationMapper $geolocationMapper,
    ) {
    }

    /**
     * @param array<IpData> $ipData
     * @return AbstractApiGeolocationResultsCollection<int, AbstractApiGeolocationResultsData>
     */
    public function geolocate(
        array $ipData,
        ?callable $intercept = null
    ): AbstractApiGeolocationResultsCollection {
        Assert::allIsInstanceOf($ipData, IpData::class);

        $requests = Collection::make($ipData)
            ->map(function (IpData $ipDataObject) {
                return GeolocationRequest::generate($ipDataObject);
            });

        $responses = Send::async($this->client, $requests->toArray());

        if ($intercept !== null) {
            $intercept($responses);
        }

        return AbstractApiGeolocationResultsCollection::make($responses)
            ->map(fn (RequestResponseWrapper $wrapper) => $this->geolocationMapper->map($wrapper));
    }
}
