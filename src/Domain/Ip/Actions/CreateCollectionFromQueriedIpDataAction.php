<?php

namespace XbNz\Resolver\Domain\Ip\Actions;

use Illuminate\Support\Collection;
use XbNz\Resolver\Domain\Ip\Collections\IpCollection;
use XbNz\Resolver\Domain\Ip\DTOs\QueriedIpData;

class CreateCollectionFromQueriedIpDataAction
{
    public function execute(Collection $queriedIpData): IpCollection
    {
        $ipCollection = [
            'query' => $queriedIpData[0]->ip,
        ];

        $queriedIpData->map(function (QueriedIpData $value, $key) use (&$ipCollection){
            $ipCollection['country'][] = [
                'driver' => $value->driver,
                'data' => $value->country,
            ];

            $ipCollection['city'][] = [
                'driver' => $value->driver,
                'data' => $value->city,
            ];

            $ipCollection['longitude'][] = [
                'driver' => $value->driver,
                'data' => (string)$value->longitude,
            ];

            $ipCollection['latitude'][] = [
                'driver' => $value->driver,
                'data' => (string)$value->latitude,
            ];
        });

        return new IpCollection($ipCollection);
    }
}