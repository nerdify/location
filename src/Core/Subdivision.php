<?php

namespace Location\Location\Core;

class Subdivision
{
    private static self $instance;

    const SUBDIVISION_SRC = __DIR__.'/../config/iso_3166-2.json';
    const SUBDIVISION_OVERRIDE_SRC = __DIR__.'/../config/override/iso_3166-2.json';
    const SUBDIVISION_TRANSLATION_SRC = __DIR__.'/../config/iso_3166-2.en-translations.json';

    public array $subdivisions = [];

    public static function make(): self
    {
        if (empty(self::$instance) || ! self::$instance instanceof self) {
            self::$instance = (new self())->load();
        }

        return self::$instance;
    }

    public function load(): self
    {
        $subdivisions = json_decode(file_get_contents(realpath(self::SUBDIVISION_SRC)), true);
        $translations = json_decode(file_get_contents(realpath(self::SUBDIVISION_TRANSLATION_SRC)), true);

        $subdivisions = $subdivisions['3166-2'];

        $results = [];

        foreach ($subdivisions as $subdivision) {
            if (isset($translations[$subdivision['code']])) {
                $subdivision['name'] = $translations[$subdivision['code']];
            }

            $results[$subdivision['code']] = $subdivision;
        }

        $subdivisions = json_decode(file_get_contents(realpath(self::SUBDIVISION_OVERRIDE_SRC)), true);

        foreach ($subdivisions as $subdivision) {
            $results[$subdivision['code']] = $subdivision;
        }

        $this->subdivisions = $results;

        return $this;
    }

    public function all(): array
    {
        return $this->subdivisions;
    }

    public function getSubdivision(string $code): ?array
    {
        return $this->subdivisions[$code] ?? null;
    }
}
