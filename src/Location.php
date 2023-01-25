<?php

namespace Location\Location;

use Location\Location\Core\Country;
use Location\Location\Core\Subdivision;
use Location\Location\Interfaces\LocationInterface;

class Location implements LocationInterface
{
    public function getCity(string $code): array
    {
        return [];
    }

    public function getCountries(): array
    {
        return Country::make()->all();
    }

    public function getCountry(string $code): array
    {
        return Country::make()->getCountry($code);
    }

    public function getSubdivision(string $code): array
    {
        return Subdivision::make()->getSubdivision($code);
    }
}
