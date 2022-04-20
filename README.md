![GitHub Workflow Status](https://img.shields.io/github/workflow/status/xbnz/laravel-multi-api/Run%20tests?label=Tests&style=for-the-badge&logo=appveyor)

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

### API downtime can disrupt your app. Plan ahead.

![Geolocation php (11)](https://user-images.githubusercontent.com/12668624/164264570-a9b99a4c-e01d-4690-8562-865654d21dc2.png)

## Geolocation

The **minimum** normalized response you can expect from each IP API. Note there may be null values, refer to NormalizedGeolocationResultsData::class for structure.

- IP address quried
- Country
- City
- Latitude
- Longitude
- Organization/ISP

![New Project (1)](https://user-images.githubusercontent.com/12668624/164261181-992717ad-e556-4d80-92ac-56e7bb18e40a.png)


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

### Downtime monitoring with the MTR.sh API

MTR.sh is a Looking Glass API that gives you access to hundreds of networks around the world. This is particularly useful for downtime monitoring. The problem with the MTR.sh API is that the result is not friendly for programming languages. This is no longer the case for Laravel developers.

#### MTR.sh search term examples:

Choose what MTR.sh networks you would like to use

| Search type | Search term                                    |
|-------------|------------------------------------------------|
| Country     | `['Germany', 'Brazil', 'Canada']`              |
| City        | `['Frankfurt', 'Rio', 'Toronto']`              |
| Continent   | `['Europe', 'South America', 'North America']` |
| UN/LOCODE   | `['defra', 'brrio', 'cator']`                  |
| ISP         | `['G-Core Labs', 'Anexia', 'Google']`          |

For a complete list, visit https://mtr.sh/probes.json

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

![New Project (2)](https://user-images.githubusercontent.com/12668624/164275491-eaf25b7d-da7a-4d7e-afb1-cc98772f7ec4.png)



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
