<?php

namespace Location\Location\Core;

class City
{
    private static self $instance;

    const CITIES_DEST = __DIR__.'/../config/geonames.json';

    public array $cities = [];

    public static function make(): self
    {
        if (empty(self::$instance) || ! self::$instance instanceof self) {
            self::$instance = (new self())->load();
        }

        return self::$instance;
    }

    public function load(): self
    {
        $cities = json_decode(file_get_contents(realpath(self::CITIES_DEST), true));

        $results = [];

        foreach ($cities as $key => $city) {
            $results[$key] = $city;
        }

        $this->cities = $results;

        return $this;
    }

    public function all(): array
    {
        return $this->cities;
    }

    public function getCity(string $code): ?array
    {
        return $this->cities[$code] ?? null;
    }
}
