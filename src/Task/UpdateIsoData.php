<?php

namespace Location\Location\Task;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class UpdateIsoData
{
    const BASE_URI = 'https://salsa.debian.org/';

    const COUNTRIES_SRC = 'iso-codes-team/iso-codes/-/raw/main/data/iso_3166-1.json';

    const SUBDIVISIONS_SRC = 'iso-codes-team/iso-codes/-/raw/main/data/iso_3166-2.json';

    const COUNTRIES_DEST = '../../src/config/iso_3166-1.json';

    const SUBDIVISIONS_DEST = '../../src/config/iso_3166-2.json';

    protected Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => self::BASE_URI,
        ]);
    }

    /**
     * @throws GuzzleException
     */
    public function loadCountries(): void
    {
        $response = $this->client->get(self::COUNTRIES_SRC);

        $data = json_decode($response->getBody()->getContents(), true);

        $countries = $data['3166-1'];

        $data = [];

        foreach ($countries as $country) {
            $data[$country['alpha_2']] = [
                'name' => $country['name'],
                'official_name' => $country['official_name'] ?? $country['name'],
            ];
        }

        file_put_contents(self::COUNTRIES_DEST, json_encode($data));
    }

    /**
     * @throws GuzzleException
     */
    public function loadSubdivisions(): void
    {
        $response = $this->client->get(self::SUBDIVISIONS_SRC);

        $data = json_decode($response->getBody()->getContents(), true);

        $subdivisions = $data['3166-2'];

        $data = [];

        foreach ($subdivisions as $subdivision) {
            $data[$subdivision['code']] = [
                'name' => $subdivision['name'],
            ];
        }

        file_put_contents(self::SUBDIVISIONS_DEST, json_encode($data));
    }
}
