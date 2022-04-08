<?php

namespace XbNz\Resolver\Support\Factories;

use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use XbNz\Resolver\Domain\Ip\DTOs\IpData;
use XbNz\Resolver\Support\DTOs\GuzzleConfigData;

class GuzzleConfigFactory
{
    public static function forIpGeolocationDotIo(IpData $ip, $overrides = []){
        $matchTypes = ['division_rivals', 'fut_draft', 'weekend_league', 'friendly'];
        $data = array_merge([
            'base_uri' => 'https://api.ipgeolocation.io/',
            'request' => new Request('GET', '/ipgeo/'),
            'query_params' => [
                'apiKey' => '4ccb1d5f495b461aa6348dd168b77d03', // TODO: Implement new random key retrieval method
                'ip' => $ip->ip,
            ],
            'middlewares' => [
                Middleware::mapRequest(static function (Request $request) {
                    return $request->withHeader('Accept', 'application/json');
                })

            ]
        ], $overrides);

        return new GuzzleConfigData(
            $data['base_uri'],
            $data['request'],
            $data['query_params'],
            $data['middlewares']
        );
    }
}