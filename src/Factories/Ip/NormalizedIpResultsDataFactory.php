<?php

namespace XbNz\Resolver\Factories\Ip;


use Illuminate\Support\Collection;
use XbNz\Resolver\Domain\Ip\DTOs\NormalizedGeolocationResultsData;
use XbNz\Resolver\Domain\Ip\DTOs\RawIpResultsData;
use XbNz\Resolver\Domain\Ip\Mappings\Mapper;

class NormalizedIpResultsDataFactory
{
    /**
     * @param array<Mapper> $mappers
     */
    public function __construct(
        private array $mappers
    )
    {}

    public function fromRaw(RawIpResultsData $ipResultsData): NormalizedGeolocationResultsData
    {
        return Collection::make($this->mappers)
            ->sole(fn (Mapper $mapper) => $mapper->supports($ipResultsData->provider))
            ->map($ipResultsData);
    }

    public function generateTestData(array $overrides = []): NormalizedGeolocationResultsData
    {
//        $data = array_merge([
//            'provider' => 'exampleprovider.io',
//            'ip' => '1.1.1.1',
//            'country' => 'Fakemenistan',
//            'city' => 'Somewheresville',
//            'longitude' => '44.44',
//            'latitude' => '55.55',
//        ], $overrides);
//
//        return new NormalizedIpResultsData(
//            driver: $data['provider'],
//            ip: $data['ip'],
//            country: $data['country'],
//            city: $data['city'],
//            longitude: $data['longitude'],
//            latitude: $data['latitude']
//        );
    }

}