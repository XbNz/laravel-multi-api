<?php

namespace XbNz\Resolver\Domain\Ip\DTOs;

use XbNz\Resolver\Support\Drivers\Driver;

class QueriedIpData extends \Spatie\DataTransferObject\DataTransferObject
{
    public string $driver;
    public string $ip;
    public string $country;
    public string $city;
    public string $longitude;
    public string $latitude;
}