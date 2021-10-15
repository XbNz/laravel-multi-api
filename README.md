

# Usage

The minimum you can expect from each API:

- Provider name
- IP address quried
- Country
- City
- ISP
- ASN
- Latitude
- Longitude

If an API does not support a piece of information, it will be null in the returned collection instance.


```php
public function example(Resolver $resolver)
{
	$providerOne = $resolver->ipGeolocation('99.99.99.99'); // IPCollection
	$resolver->ipInfo('99.99.99.99'); // IPCollection
	$resolver->ipApi('99.99.99.99'); // IPCollection


	$providerOne->getCity(); // ['Portland']
	$providerOne->getCountry(); // ['United States']

	// ...

}

```

The return for ONE provider will look like this:

```bash
=> Illuminate\Support\Collection {#3693
     all: [
       // Guaranteed returns
       "query" => "99.99.99.99",
       "country" => [
           [
           	  "provider" => "IpGeolocation",
           	  "data" => "United States"
           ]
       ],
       "city" => [
           [
           	  "provider" => "IpGeolocation",
           	  "data" => "Portland"
           ]
       ],
       "isp" => [
           [
           	  "provider" => "IpGeolocation",
           	  "data" => "Verizon"
           ]
       ],
       "asn" => [
           [
           	  "provider" => "IpGeolocation",
           	  "data" => "12345"
           ]
       ],
       "latitude" => [
           [
           	  "provider" => "IpGeolocation",
           	  "data" => 45.33
           ]
       ],
       "longitude" => [
           [
           	  "provider" => "IpGeolocation",
           	  "data" => 34.65
           ]
       ],

       // Possible returns
       "state" => null,
       "blacklist" => null,
       "type" => [
           [
           	  "provider" => "IpGeolocation",
           	  "data" => "Datacenter"
           ]
       ]
     ],
   }
```

## Combining all of your APIs

You can receive complete information for an IP using all of your APIs to put together a comprehensive report. Note this will use more API hits and take longer.


```php
public function example(Resolver $resolver)
{
	$resolver
        ->ipApi()
        ->ipInfo()
        ->ipGeolocation()
        ->maxMind() 
        ->execute('9.9.9.9') // IPCollection
}

```

This will use all of your APIs/CSV files, the return will be:


```bash
=> Illuminate\Support\Collection {#3693
     all: [
       // Guaranteed returns
       "query" => "99.99.99.99",
       "country" => [
           [
           	  "provider" => "IpGeolocation",
           	  "data" => "United States"
           ],
           [
           	  "provider" => "IpInfo",
           	  "data" => "United States"
           ],
           [
           	  "provider" => "IpApi",
           	  "data" => "United States"
           ],
       ],
       "city" => [
           [
           	  "provider" => "IpGeolocation",
           	  "data" => "Portland"
           ],
           [
           	  "provider" => "IpInfo",
           	  "data" => "Seattle"
           ],
           [
           	  "provider" => "IpApi",
           	  "data" => "Seattle"
           ],
       ],
       "isp" => [
           [
           	  "provider" => "IpGeolocation",
           	  "data" => "Verizon"
           ],
           [
           	  "provider" => "IpInfo",
           	  "data" => "Verizon Ltd."
           ],
           [
           	  "provider" => "IpApi",
           	  "data" => "Verizon Limited"
           ],
       ],
       "asn" => [
           [
           	  "provider" => "IpGeolocation",
           	  "data" => "12345"
           ],
           [
           	  "provider" => "IpInfo",
           	  "data" => "12345"
           ],
           [
           	  "provider" => "IpApi",
           	  "data" => "12345"
           ],
       ],
       "latitude" => [
           [
           	  "provider" => "IpGeolocation",
           	  "data" => 44.33
           ],
           [
           	  "provider" => "IpInfo",
           	  "data" => 45.54
           ],
           [
           	  "provider" => "IpApi",
           	  "data" => 45.54
           ],
       ],
       "longitude" => [
           [
           	  "provider" => "IpGeolocation",
           	  "data" => 34.33
           ],
           [
           	  "provider" => "IpInfo",
           	  "data" => 35.54
           ],
           [
           	  "provider" => "IpApi",
           	  "data" => 35.54
           ],
       ],

       // Possible returns

       "state" => [
           [
           	  "provider" => "IpGeolocation",
           	  "data" => "Oregon"
           ],
           [
           	  "provider" => "IpInfo",
           	  "data" => "Washington"
           ],
       ],
       "blacklist" => [
           [
           	  "provider" => "Maxmind",
           	  "data" => \Whatever\Whatever\Blacklist::class
           ],
       ],
       "type" => [
           [
           	  "provider" => "IpGeolocation",
           	  "data" => "Datacenter"
           ],
           [
           	  "provider" => "Maxmind",
           	  "data" => "Datacenter"
           ]
       ]
     ],
   }
```