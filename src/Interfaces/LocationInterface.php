<?php

namespace Location\Location\Interfaces;

interface LocationInterface
{
    public function getCity(string $code): array;

    public function getCountries(): array;

    public function getCountry(string $code): array;

    public function getSubdivision(string $code): array;
}
