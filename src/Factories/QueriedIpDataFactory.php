<?php

namespace XbNz\Resolver\Factories;


use XbNz\Resolver\Domain\Ip\Drivers\IpInfoDriverDotIoDriver;
use XbNz\Resolver\Domain\Ip\DTOs\QueriedIpData;

class QueriedIpDataFactory
{
    public static function generateTestData(array $overrides = []): QueriedIpData
    {
        $data = array_merge([
            'driver' => IpInfoDriverDotIoDriver::class,
            'ip' => '1.1.1.1',
            'country' => 'Fakemenistan',
            'city' => 'Somewheresville',
            'longitude' => '44.44',
            'latitude' => '55.55',
        ], $overrides);

        return new QueriedIpData(
            driver: $data['driver'],
            ip: $data['ip'],
            country: $data['country'],
            city: $data['city'],
            longitude: $data['longitude'],
            latitude: $data['latitude']
        );
    }

}