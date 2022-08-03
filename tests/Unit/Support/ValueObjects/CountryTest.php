<?php

declare(strict_types=1);

namespace XbNz\Resolver\Tests\Unit\Support\ValueObjects;

use Generator;
use League\ISO3166\ISO3166;
use XbNz\Resolver\Support\ValueObjects\Country;
use XbNz\Resolver\Tests\TestCase;

class CountryTest extends TestCase
{
    /**
     * @test
     * @dataProvider countryProvider
     **/
    public function it_can_resolve_an_iso_3166_alpha_2_country(array $country, string $key): void
    {
        // Arrange

        // Act
        $resolvedCountry = Country::from($country['alpha2']);

        // Assert
        $this->assertSame($country['name'], $resolvedCountry->name);
        $this->assertSame($country['alpha2'], $resolvedCountry->alpha2);
        $this->assertSame($country['alpha3'], $resolvedCountry->alpha3);
        $this->assertSame($country['numeric'], $resolvedCountry->numeric);
        $this->assertSame($country['currency'], $resolvedCountry->currencies);
    }

    /**
     * @test
     * @dataProvider countryProvider
     **/
    public function it_can_resolve_an_iso_3166_alpha_3_country(array $country, string $key): void
    {
        // Arrange

        // Act
        $resolvedCountry = Country::from($country['alpha3']);

        // Assert
        $this->assertSame($country['name'], $resolvedCountry->name);
        $this->assertSame($country['alpha2'], $resolvedCountry->alpha2);
        $this->assertSame($country['alpha3'], $resolvedCountry->alpha3);
        $this->assertSame($country['numeric'], $resolvedCountry->numeric);
        $this->assertSame($country['currency'], $resolvedCountry->currencies);
    }

    /**
     * @test
     * @dataProvider countryProvider
     **/
    public function it_can_resolve_a_country_name(array $country, string $key): void
    {
        // Arrange

        // Act
        $resolvedCountry = Country::from($country['name']);

        // Assert
        $this->assertSame($country['name'], $resolvedCountry->name);
        $this->assertSame($country['alpha2'], $resolvedCountry->alpha2);
        $this->assertSame($country['alpha3'], $resolvedCountry->alpha3);
        $this->assertSame($country['numeric'], $resolvedCountry->numeric);
        $this->assertSame($country['currency'], $resolvedCountry->currencies);
    }

    public function countryProvider(): Generator
    {
        $countries = (new ISO3166())->all();

        foreach ($countries as $country) {
            yield $country['name'] => [
                'country' => $country,
                'key' => $country['name'],
            ];
        }
    }
}
