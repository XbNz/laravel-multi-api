<?php

declare(strict_types=1);

namespace XbNz\Resolver\Tests\Unit\Support\ValueObjects;

use XbNz\Resolver\Support\ValueObjects\Continent;
use XbNz\Resolver\Tests\TestCase;

class ContinentTest extends TestCase
{
    /** @test **/
    public function it_can_resolve_a_continent_by_code(): void
    {
        // Arrange

        // Act
        $asia = Continent::fromCode('AS');
        $africa = Continent::fromCode('AF');
        $northAmerica = Continent::fromCode('NA');
        $southAmerica = Continent::fromCode('SA');
        $antarctica = Continent::fromCode('AN');
        $europe = Continent::fromCode('EU');
        $australia = Continent::fromCode('AU');

        // Assert
        $this->assertSame('Asia', $asia->name);
        $this->assertSame('AS', $asia->code);

        $this->assertSame('Africa', $africa->name);
        $this->assertSame('AF', $africa->code);

        $this->assertSame('North America', $northAmerica->name);
        $this->assertSame('NA', $northAmerica->code);

        $this->assertSame('South America', $southAmerica->name);
        $this->assertSame('SA', $southAmerica->code);

        $this->assertSame('Antarctica', $antarctica->name);
        $this->assertSame('AN', $antarctica->code);

        $this->assertSame('Europe', $europe->name);
        $this->assertSame('EU', $europe->code);

        $this->assertSame('Australia', $australia->name);
        $this->assertSame('AU', $australia->code);
    }

    /** @test **/
    public function it_can_resolve_a_continent_by_name(): void
    {
        // Arrange

        // Act

        $asia = Continent::fromName('Asia');
        $africa = Continent::fromName('Africa');
        $northAmerica = Continent::fromName('North America');
        $southAmerica = Continent::fromName('South America');
        $antarctica = Continent::fromName('Antarctica');
        $europe = Continent::fromName('Europe');
        $australia = Continent::fromName('Australia');

        // Assert
        $this->assertSame('Asia', $asia->name);
        $this->assertSame('AS', $asia->code);

        $this->assertSame('Africa', $africa->name);
        $this->assertSame('AF', $africa->code);

        $this->assertSame('North America', $northAmerica->name);
        $this->assertSame('NA', $northAmerica->code);

        $this->assertSame('South America', $southAmerica->name);
        $this->assertSame('SA', $southAmerica->code);

        $this->assertSame('Antarctica', $antarctica->name);
        $this->assertSame('AN', $antarctica->code);

        $this->assertSame('Europe', $europe->name);
        $this->assertSame('EU', $europe->code);

        $this->assertSame('Australia', $australia->name);
        $this->assertSame('AU', $australia->code);
    }
}
