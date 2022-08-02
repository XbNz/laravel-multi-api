<?php

namespace XbNz\Resolver\Support\ValueObjects;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ItemNotFoundException;
use Illuminate\Support\Str;
use League\ISO3166\Exception\DomainException;
use League\ISO3166\ISO3166;
use Locale;
use Webmozart\Assert\Assert;

class Country
{
    /**
     * @param array<int, string> $currencies
     */
    public function __construct(
        public readonly string $alpha2,
        public readonly string $alpha3,
        public readonly string $name,
        public readonly string $numeric,
        public readonly array $currencies,
    ) {
    }

    public static function from(string $string): self
    {
        Assert::stringNotEmpty($string);
        // TODO: Pick up here: country value object is complete. Continue with rest of ipgeolocation json
        $string = Str::of($string)->lower()->value();

        $country = rescue(
            static fn() => self::tryIso($string),
            static fn() => self::tryName($string)
        );

        return new self(
            $country['alpha2'],
            $country['alpha3'],
            $country['name'],
            $country['numeric'],
            $country['currency']
        );
    }

    /**
     * @return array{'name': string, 'alpha2': string, 'alpha3': string, 'numeric': string, 'currency': array<int, string>}
     */
    public static function tryIso(string $string): array
    {
        return rescue(
            static fn() => (new ISO3166)->alpha2($string),
            static fn() => (new ISO3166)->alpha3($string)
        );
    }

    /**
     * @return array{'name': string, 'alpha2': string, 'alpha3': string, 'numeric': string, 'currency': array<int, string>}
     */
    public static function tryName(string $string): array
    {
        $countries = Collection::make((new ISO3166())->all());

        try {
            return $countries->sole(function ($country) use ($string) {
                $lower = Str::of($country['name'])->lower()->value();
                return Str::of($lower)->contains($string);
            });
        } catch (ItemNotFoundException) {
            throw new DomainException;
        }
    }
}