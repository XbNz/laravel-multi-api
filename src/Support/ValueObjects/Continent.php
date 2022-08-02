<?php

namespace XbNz\Resolver\Support\ValueObjects;

use Illuminate\Support\Str;
use Locale;

class Continent
{
    public function __construct(
        private readonly string $code,
        private readonly string $name,
    ) {
    }

    public static function fromCode(string $string): self
    {
        $string = Str::of($string)->lower()->value();
        return match ($string) {
            'as' => new self('AS', 'Asia'),
            'af' => new self('AF', 'Africa'),
            'na' => new self('NA', 'North America'),
            'sa' => new self('SA', 'South America'),
            'an' => new self('AN', 'Antarctica'),
            'eu' => new self('EU', 'Europe'),
            'au' => new self('AU', 'Australia'),
        };
    }

    public static function fromName(string $string)
    {
        $string = Str::of($string)->lower()->value();
        return match ($string) {
            'asia' => new self('AS', 'Asia'),
            'africa' => new self('AF', 'Africa'),
            'north america' => new self('NA', 'North America'),
            'south america' => new self('SA', 'South America'),
            'antarctica' => new self('AN', 'Antarctica'),
            'europe' => new self('EU', 'Europe'),
            'australia' => new self('AU', 'Australia'),
        };
    }
}