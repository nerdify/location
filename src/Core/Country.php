<?php

namespace Location\Location\Core;

class Country
{
    private static self $instance;

    const COUNTRIES_DEST = __DIR__.'/../config/iso_3166-1.json';
    const COUNTRIES_OVERRIDE_DEST = __DIR__.'/../config/override/iso_3166-1.json';

    public array $countries = [];

    public static function make(): self
    {
        if (empty(self::$instance) || ! self::$instance instanceof self) {
            self::$instance = (new self())->load();
        }

        return self::$instance;
    }

    public function load(): self
    {
        $countries = json_decode(file_get_contents(realpath(self::COUNTRIES_DEST)), true);

        $results = [];

        foreach ($countries as $key => $country) {
            $results[$key] = $country;
        }

        $countries = json_decode(file_get_contents(realpath(self::COUNTRIES_OVERRIDE_DEST)), true);

        foreach ($countries as $key => $country) {
            $results[$key] = $country;
        }

        $this->countries = $results;

        return $this;
    }

    public function all(): array
    {
        return $this->countries;
    }

    public function getCountry(string $code): ?array
    {
        return $this->countries[$code] ?? null;
    }
}
