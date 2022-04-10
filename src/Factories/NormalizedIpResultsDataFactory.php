<?php

namespace XbNz\Resolver\Factories;


use Psr\Http\Message\ResponseInterface;
use XbNz\Resolver\Domain\Ip\Drivers\IpInfoDotIoDriver;
use XbNz\Resolver\Domain\Ip\DTOs\NormalizedIpResultsData;
use XbNz\Resolver\Domain\Ip\DTOs\RawIpResultsData;

class NormalizedIpResultsDataFactory
{
    public function fromResponse(RawIpResultsData $ipResultsData): NormalizedIpResultsData
    {

    }

    public function generateTestData(array $overrides = []): NormalizedIpResultsData
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