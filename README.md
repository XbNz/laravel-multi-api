![GitHub Workflow Status](https://img.shields.io/github/workflow/status/xbnz/laravel-multi-ip/Run%20tests?label=Tests&style=for-the-badge&logo=appveyor)

# Laravel Multi API

## Supported APIs

| Provider         | Key required | Normalized Object                       |
|------------------|--------------|-----------------------------------------|
| Abstractapi.com  | ✅            | NormalizedGeolocationResultsData::class |
| Abuseipdb.com    | ✅            | NormalizedGeolocationResultsData::class |
| Ipapi.co         | ❌            | NormalizedGeolocationResultsData::class |
| Ipapi.com        | ✅            | NormalizedGeolocationResultsData::class |
| Ip-api.com       | ❌            | NormalizedGeolocationResultsData::class |
| Ipdata.co        | ✅            | NormalizedGeolocationResultsData::class |
| Ipgeolocation.io | ✅            | NormalizedGeolocationResultsData::class |
| Ipinfo.io        | ✅            | NormalizedGeolocationResultsData::class |
| Mtr.sh: ping     | ❌            | MtrDotShPingResultsData::class          |
| Mtr.sh: mtr      | ❌            | MtrDotShMtrResultsData::class           |


## Installation

```bash
composer require xbnz/laravel-multi-api
```


Ensure that your composer.json file tells Laravel to auto-wire package service providers to your project:

```json
"extra": {
  "laravel": {
    "dont-discover": []
  }
},
```

### The config files

- Publish config files:

```bash
php artisan vendor:publish --tag=ip-resolver
php artisan vendor:publish --tag=resolver
```

```php
// resolver.php

'use_proxy' => (bool),
'proxies' => (array<string>), // https://13.44.34.34:8080, https://user:pass@33.22.55.66:8080

'timeout' => (int), // seconds
'cache_period' => (int) //seconds,

'async_concurrent_requests' => (int),

'use_retries' => (bool),
'tries' => (int),
'retry_sleep' => (float), // seconds
'retry_sleep_multiplier' => (float) // seconds,
```


```php
// ip-resolver.php

'api-keys' => (array<Driver>),

/**
  * IpGeolocationDotIoDriver::class => [env(KEY_1), env(KEY_2), ...],
 */

/**
 * Visit https://mtr.sh/probes.json to retrieve the list of probe IDs
 */
\XbNz\Resolver\Domain\Ip\Drivers\MtrDotShMtrDriver::class => [
    'search' => (array<string>)
],

\XbNz\Resolver\Domain\Ip\Drivers\MtrDotShPingDriver::class => [
    'search' => (array<string>)
],

```

### Support for multiple API keys

You may configure each driver in the config files with multiple API keys. API keys will be chosen randomly per HTTP request. If you have elected to use retry functionality, the key will be rehydrated on every try.  


## Caching

Caching is enforced by default as the alternative with exhaust your rate limiting very quickly.
If you are using a time-sensitive service, use Laravel's Config facade to reduce caching before your API call.
```php
Config::set(['resolver.cache_period' => 1]);
``` 

### Proxies

HTTP, HTTPS, SOCKS supported. Please use the URL structure denoted above. If you have elected to use retry functionality, the proxy will be rehydrated on every try.

## Usage

The **minimum** normalized response you can expect from each IP API. Note there may be null values, refer to NormalizedGeolocationResultsData::class for structure.

- IP address quried
- Country
- City
- Latitude
- Longitude
- Organization/ISP


### Combining all of your APIs

You can receive complete information for an IP using all of your APIs to put together a comprehensive report. Increase your async value in the `resolver.php` config file to expedite the process if you have many IPs and drivers. 

```php
public function example(Resolver $resolver)
{
	$result = $resolver
	    ->ip()
	    ->withIps(['8.8.8.8', '2606:4700:4700::1111'])
	    ->ipGeolocationDotIo()
	    ->ipApiDotCom()
	    ->ipInfoDotIo()
	    ->normalize();
	// ...

}
```

The return will look like this:

```
  0 => XbNz\Resolver\Domain\Ip\DTOs\NormalizedGeolocationResultsData^ {#860
    +provider: "XbNz\Resolver\Domain\Ip\Drivers\AbstractApiDotComDriver"
    +ip: "50.216.94.33"
    +country: "United States"
    +city: null
    +latitude: 37.751
    +longitude: -97.822
    +organization: "Comcast Cable Communications"
  }
  1 => XbNz\Resolver\Domain\Ip\DTOs\NormalizedGeolocationResultsData^ {#859
    +provider: "XbNz\Resolver\Domain\Ip\Drivers\IpGeolocationDotIoDriver"
    +ip: "50.216.94.33"
    +country: "United States"
    +city: "Mt Laurel"
    +latitude: 39.96657
    +longitude: -74.90327
    +organization: "Comcast Cable Communications, LLC"
  }
  2 => XbNz\Resolver\Domain\Ip\DTOs\NormalizedGeolocationResultsData^ {#857
    +provider: "XbNz\Resolver\Domain\Ip\Drivers\IpApiDotCoDriver"
    +ip: "50.216.94.33"
    +country: "United States"
    +city: "New Haven"
    +latitude: 42.7876
    +longitude: -82.8007
    +organization: "COMCAST-33491"

```

### Raw API output

If you do not wish to receive condensed, normalized information, you may use the raw method:

```php
public function example(Resolver $resolver)
{
	$result = $resolver
        ->ip()
        ->withIps(['8.8.8.8', '2606:4700:4700::1111'])
        ->ipGeolocationDotIo()
        ->ipApiDotCom()
        ->ipInfoDotIo()
        ->raw();
    // ...
}
```

```
 0 => XbNz\Resolver\Support\DTOs\RawResultsData^ {#801
    +provider: "XbNz\Resolver\Domain\Ip\Drivers\SomeApiDriver"
    +data: array:21 [
      "ip_address" => "50.216.94.33"
      "city" => '...'
      "city_geoname_id" => '...'
      "region" => '...'
      // ...
    ]
  }
  1 => XbNz\Resolver\Support\DTOs\RawResultsData^ {#801
    +provider: "XbNz\Resolver\Domain\Ip\Drivers\AnotherDriver"
    +data: array:21 [
      "ip" => "50.216.94.34"
      "city_name" => '...'
      "city_code" => '...'
      "region_name" => '...'
      // ...
    ]
  }
  

```

| ℹ️ This works in the same way as normalize(). Keep in mind there is no guarantee of data integrity with this option, the result is returned directly from the API in most cases. |
|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|


### Alternative to chaining

```php
public function example(Resolver $resolver)
{
    $result = $resolver
        ->ip()
        ->withIps(['8.8.8.8', '2606:4700:4700::1111'])
        ->withDrivers([
            IpGeolocationDotIoDriver::class,
            // other drivers...
        ])
        ->normalize();
    // ...
}
```

### Ping and MTR tests

#### MTR.sh search term examples:

| Search type | Search term                                    |
|-------------|------------------------------------------------|
| Country     | `['Germany', 'Brazil', 'Canada']`              |
| City        | `['Frankfurt', 'Rio', 'Toronto']`              |
| Continent   | `['Europe', 'South America', 'North America']` |
| UN/LOCODE   | `['defra', 'brrio', 'cator']`                  |
| ISP         | `['G-Core Labs', 'Anexia', 'Google']`          |

https://mtr.sh/probes.json

```php
public function example(Resolver $resolver)
{
    $result = $resolver
        ->ip()
        ->withIps(['1.1.1.1'])
        ->mtrDotShMtr()
        ->mtrDotShPing()
        ->normalize();
    // ...

}
```

| ℹ️ Some MTR.sh probes may not support IPv6, or may not have some abilities, such as the ability to perform MTR tests. When you specify a search term, if no probes match the IP or test type capability, `MtrProbeNotFoundException::class` will be thrown |
|-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|


#### The MTR result:

```
 0 => XbNz\Resolver\Domain\Ip\DTOs\MtrDotSh\MtrDotShMtrResultsData^ {#726
    +probe: XbNz\Resolver\Domain\Ip\DTOs\MtrDotSh\MtrDotShProbeData^ {#614
      +probeId: "7xWtI"
      +asNumber: 42473
      +city: "Vienna"
      +country: "Austria"
      +continent: "Europe"
      +provider: "Anexia"
      +providerUrl: "https://anexia.com/"
      +unLoCode: "atvie"
      +canPerformMtr: true
      +canPerformDnsTrace: true
      +canPerformTraceroute: true
      +canPerformDnsResolve: true
      +canPerformPing: true
      +isOnline: true
      +residential: false
      +supportsVersion4: true
      +supportsVersion6: true
    }
    +targetIp: XbNz\Resolver\Domain\Ip\DTOs\IpData^ {#784
      +ip: "1.1.1.1"
      +version: 4
    }
    +hops: Illuminate\Support\Collection^ {#606
      #items: array:10 [
        0 => XbNz\Resolver\Domain\Ip\DTOs\MtrDotSh\MtrDotShMtrHopResultsData^ {#603
          +hopPositionCount: 1
          +hopHost: "94.16.98.160"
          +packetLossPercentage: 0.0
          +droppedPackets: 0
          +receivedPackets: 10
          +sentPackets: 10
          +lastRttValue: 0.3
          +lowestRttValue: 0.2
          +averageRttValue: 2.9
          +highestRttValue: 26.4
          +standardDeviation: 8.3
          +geometricMean: 0.4
          +jitter: 0.1
          +averageJitter: 5.3
          +maximumJitter: 26.2
          +interarrivalJitter: 35.0
        }
        1 => XbNz\Resolver\Domain\Ip\DTOs\MtrDotSh\MtrDotShMtrHopResultsData^ {#604
          +hopPositionCount: 2
          +hopHost: "ae12-0.bbr01.anx04.vie.at.anexia-it.net | (144.208.208.218)"
          +packetLossPercentage: 0.0
          +droppedPackets: 0
          +receivedPackets: 10
          +sentPackets: 10
          +lastRttValue: 12.8
          +lowestRttValue: 12.6
          +averageRttValue: 12.7
          +highestRttValue: 12.8
          +standardDeviation: 0.1
          +geometricMean: 12.7
          +jitter: 0.1
          +averageJitter: 0.1
          +maximumJitter: 0.1
          +interarrivalJitter: 0.5
        }
        // removed hops for brevity...
    }
  }
  1 => XbNz\Resolver\Domain\Ip\DTOs\MtrDotSh\MtrDotShMtrResultsData^ {#716
    +probe: XbNz\Resolver\Domain\Ip\DTOs\MtrDotSh\MtrDotShProbeData^ {#692
      +probeId: "2ctCE"
      +asNumber: 42473
      +city: "Vienna"
      +country: "Austria"
      +continent: "Europe"
      +provider: "Anexia"
      +providerUrl: "https://anexia.com/"
      +unLoCode: "atvie"
      +canPerformMtr: true
      +canPerformDnsTrace: true
      +canPerformTraceroute: true
      +canPerformDnsResolve: true
      +canPerformPing: true
      +isOnline: true
      +residential: false
      +supportsVersion4: true
      +supportsVersion6: true
    }
    +targetIp: XbNz\Resolver\Domain\Ip\DTOs\IpData^ {#798
      +ip: "1.1.1.1"
      +version: 4
    }
    +hops: Illuminate\Support\Collection^ {#787
      #items: array:9 [
        0 => XbNz\Resolver\Domain\Ip\DTOs\MtrDotSh\MtrDotShMtrHopResultsData^ {#724
          +hopPositionCount: 1
          +hopHost: "45.84.253.142"
          +packetLossPercentage: 0.0
          +droppedPackets: 0
          +receivedPackets: 10
          +sentPackets: 10
          +lastRttValue: 0.3
          +lowestRttValue: 0.3
          +averageRttValue: 9.5
          +highestRttValue: 51.2
          +standardDeviation: 19.3
          +geometricMean: 1.1
          +jitter: 0.1
          +averageJitter: 14.2
          +maximumJitter: 50.9
          +interarrivalJitter: 100.0
        }
        1 => XbNz\Resolver\Domain\Ip\DTOs\MtrDotSh\MtrDotShMtrHopResultsData^ {#725
          +hopPositionCount: 2
          +hopHost: "ae1-0.bbr01.anx03.vie.at.anexia-it.net | (144.208.208.134)"
          +packetLossPercentage: 0.0
          +droppedPackets: 0
          +receivedPackets: 10
          +sentPackets: 10
          +lastRttValue: 12.6
          +lowestRttValue: 12.5
          +averageRttValue: 31.2
          +highestRttValue: 193.4
          +standardDeviation: 57.0
          +geometricMean: 17.1
          +jitter: 0.9
          +averageJitter: 36.9
          +maximumJitter: 180.0
          +interarrivalJitter: 333.6
        }
        // removed hops for brevity...
      ]
    }
  }
```

#### The Ping result:

```
 2 => XbNz\Resolver\Domain\Ip\DTOs\MtrDotSh\MtrDotShPingResultsData^ {#715
    +probe: XbNz\Resolver\Domain\Ip\DTOs\MtrDotSh\MtrDotShProbeData^ {#629
      +probeId: "1F7As"
      +asNumber: 24940
      +city: "Falkenstein"
      +country: "Germany"
      +continent: "Europe"
      +provider: "Hetzner"
      +providerUrl: "https://www.hetzner.de/"
      +unLoCode: "defks"
      +canPerformMtr: true
      +canPerformDnsTrace: true
      +canPerformTraceroute: true
      +canPerformDnsResolve: true
      +canPerformPing: true
      +isOnline: true
      +residential: false
      +supportsVersion4: true
      +supportsVersion6: true
    }
    +targetIp: XbNz\Resolver\Domain\Ip\DTOs\IpData^ {#813
      +ip: "1.1.1.1"
      +version: 4
    }
    +packetLossPercentage: 0.0
    +sequences: Illuminate\Support\Collection^ {#797
      #items: array:10 [
        0 => XbNz\Resolver\Domain\Ip\DTOs\MtrDotSh\MtrDotShPingSequenceResultsData^ {#795
          +size: 64
          +ip: "1.1.1.1"
          +sequenceNumber: 0
          +timeToLive: 55
          +roundTripTime: 12.1
        }
        1 => XbNz\Resolver\Domain\Ip\DTOs\MtrDotSh\MtrDotShPingSequenceResultsData^ {#796
          +size: 64
          +ip: "1.1.1.1"
          +sequenceNumber: 1
          +timeToLive: 55
          +roundTripTime: 12.1
        }
        // removed sequences for brevity...
      ]
    }
    +statistics: XbNz\Resolver\Domain\Ip\DTOs\MtrDotSh\MtrDotShPingStatisticsResultsData^ {#605
      +minimumRoundTripTime: 12.1
      +averageRoundTripTime: 12.63
      +maximumRoundTripTime: 14.5
      +jitter: 0.53333333333333
    }
  }
  3 => XbNz\Resolver\Domain\Ip\DTOs\MtrDotSh\MtrDotShPingResultsData^ {#812
    +probe: XbNz\Resolver\Domain\Ip\DTOs\MtrDotSh\MtrDotShProbeData^ {#706
      +probeId: "1F7As"
      +asNumber: 24940
      +city: "Falkenstein"
      +country: "Germany"
      +continent: "Europe"
      +provider: "Hetzner"
      +providerUrl: "https://www.hetzner.de/"
      +unLoCode: "defks"
      +canPerformMtr: true
      +canPerformDnsTrace: true
      +canPerformTraceroute: true
      +canPerformDnsResolve: true
      +canPerformPing: true
      +isOnline: true
      +residential: false
      +supportsVersion4: true
      +supportsVersion6: true
    }
    +targetIp: XbNz\Resolver\Domain\Ip\DTOs\IpData^ {#828
      +ip: "1.1.1.1"
      +version: 4
    }
    +packetLossPercentage: 0.0
    +sequences: Illuminate\Support\Collection^ {#613
      #items: array:10 [
        0 => XbNz\Resolver\Domain\Ip\DTOs\MtrDotSh\MtrDotShPingSequenceResultsData^ {#810
          +size: 64
          +ip: "1.1.1.1"
          +sequenceNumber: 0
          +timeToLive: 56
          +roundTripTime: 52.9
        }
        1 => XbNz\Resolver\Domain\Ip\DTOs\MtrDotSh\MtrDotShPingSequenceResultsData^ {#811
          +size: 64
          +ip: "1.1.1.1"
          +sequenceNumber: 1
          +timeToLive: 56
          +roundTripTime: 49.1
        }
        // removed sequences for brevity...
      ]
    }
    +statistics: XbNz\Resolver\Domain\Ip\DTOs\MtrDotSh\MtrDotShPingStatisticsResultsData^ {#802
      +minimumRoundTripTime: 12.7
      +averageRoundTripTime: 29.85
      +maximumRoundTripTime: 68.1
      +jitter: 11.2
    }
```


## A quick word on the design

If you would like to extend the package to support other APIs, please keep the following in mind:
- One driver per endpoint
-- GET: https://someapi.io/geo/1.1.1.1: SomeApiGeoDriver::class
-- POST: https://someapi.io/geo/bulk: SomeApiGeoBulkDriver::class

- `AuthStrategies` & `RetryStrategies` are responsible for applying api key headers, paths and query params, not the driver.
- `Normalize()` functionality will only work if there is a `Mapper::class` that `supports()` the target `Driver::class`

- Mappers, Drivers, and Strategies are all registered in the `ResolverServiceProvider::class` & `IpServiceProvider::class`
- New API categories like currency conversion API drivers will follow the same pattern: registered in a theoretical `CurrencyServiceProvider::class`

## Contributing
Pull requests and issues are welcome.

## License
[MIT](./LICENSE.md)