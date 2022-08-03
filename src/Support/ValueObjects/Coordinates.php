<?php

declare(strict_types=1);

namespace XbNz\Resolver\Support\ValueObjects;

use Webmozart\Assert\Assert;

class Coordinates
{
    public function __construct(
        public float $latitude,
        public float $longitude,
    ) {
        Assert::greaterThanEq($latitude, -90);
        Assert::lessThanEq($latitude, 90);
        Assert::greaterThanEq($longitude, -180);
        Assert::lessThanEq($longitude, 180);
    }

    public static function from(float $latitude, float $longitude): self
    {
        return new self($latitude, $longitude);
    }
}
