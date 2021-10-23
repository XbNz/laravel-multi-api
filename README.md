![GitHub Workflow Status](https://img.shields.io/github/workflow/status/xbnz/laravel-multi-ip/Run%20tests?label=Tests&style=for-the-badge&logo=appveyor)

# Laravel Multi IP

## Installation

```bash
composer require xbnz/laravel-multi-ip
```

## Configuration

Ensure that your composer.json file tells Laravel to auto-wire package service providers to your project:

```json
"extra": {
  "laravel": {
    "dont-discover": []
  }
},
```

- Publish config files:

```bash
php artisan vendor:publish --tag=ip-resolver
php artisan vendor:publish --tag=resolver
```

```php
// resolver.php

'use_proxy' => (bool),
'proxies' => ['https://2.2.2.2:8080', 'https://user:pass@2.2.2.2:8080'],
'timeout' => (int) //seconds,
'cache_period' => (int) // seconds,
'use_retries' => (bool),
'tries' => (int),
'retry_sleep' => (int) // milliseconds
```

### Proxies

HTTP & HTTPS supported. Please use the URL structure denoted above. If you have elected to use retry functionality, the proxy will be rehydrated on every try.

```php
// ip-resolver.php

'api-keys' => [
    'driver' => [
        'key_1', 'key_2', 'key_3' //...
    ]   
],

/*
 * Not supported in v1
 */
'databases' => [
    'driver_name' => [
        'v4' => 'table_name_v4'
        'v6' => 'table_name_v6'
    ]
]
```

### Support for multiple API keys

You may configure each driver in the config files with multiple API keys. API keys will be chosen randomly per HTTP request. If you have elected to use retry functionality, the key will be rehydrated on every try.  


## Caching

Caching is enforced by default as the alternative with exhaust your rate limiting very quickly. This package will use your default application cache driver and save keys in the following format:
```php
Driver::class . {$entity}
``` 

Entity may be an IP or anything else the driver is responsible for.

## Usage

The **minimum** normalized response you can expect from each API:

- IP address quried
- Country
- City
- Latitude
- Longitude


## Combining all of your APIs

You can receive complete information for an IP using all of your APIs to put together a comprehensive report. Note this will use more API hits and take longer.

```php
public function example(Resolver $resolver)
{
	$result = $resolver
	    ->ip()
	    ->withIp('8.8.8.8')
	    ->ipGeolocationDotIo()
	    ->ipApiDotCom()
	    ->ipInfoDotIo()
	    ->normalize();
	// ...

}
```

The return will look like this:

```bash
=> Illuminate\Support\Collection {#3693
     all: [
       "query" => "8.8.8.8",
       "country" => [
           [
           	  "driver" => "IpGeolocationDotIoDriver::class",
           	  "data" => "United States"
           ],
           [
           	  "driver" => "ipApiDotComDriver::class",
           	  "data" => "United States"
           ],
           [
           	  "driver" => "IpInfoDotIoDriver::class",
           	  "data" => "United States"
           ]
       ],
       "city" => [
            [
           	  "driver" => "IpGeolocationDotIoDriver::class",
           	  "data" => "Portland"
           ],
           [
           	  "driver" => "ipApiDotComDriver::class",
           	  "data" => "Seattle"
           ],
           [
           	  "driver" => "IpInfoDotIoDriver::class",
           	  "data" => "Los Angeles"
           ]
       ],
       "latitude" => [
            [
           	  "driver" => "IpGeolocationDotIoDriver::class",
           	  "data" => "33.43"
           ],
           [
           	  "driver" => "ipApiDotComDriver::class",
           	  "data" => "36.63"
           ],
           [
           	  "driver" => "IpInfoDotIoDriver::class",
           	  "data" => "31.43"
           ]
       ],
       "longitude" => [
           [
           	  "driver" => "IpGeolocationDotIoDriver::class",
           	  "data" => "23.43"
           ],
           [
           	  "driver" => "ipApiDotComDriver::class",
           	  "data" => "32.63"
           ],
           [
           	  "driver" => "IpInfoDotIoDriver::class",
           	  "data" => "11.43"
           ]
       ],
     ],
   }
```

## Raw API output

If you do not wish to receive condensed, normalized information, you may use the raw method:

```php
public function example(Resolver $resolver)
{
	$result = $resolver
	    ->ip()
	    ->withIp('8.8.8.8')
	    ->ipGeolocationDotIo()
	    ->ipApiDotCom()
	    ->ipInfoDotIo()
	    ->raw();
	// ...
}
```

This works in the same way as normalize() and supports chaining. Keep in mind there is no guarantee of data integrity with this option.



## Contributing
Pull requests and issues are welcome.

## License
[MIT](./LICENSE.md)