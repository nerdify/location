<?php

namespace Location\Location\Task\Scrapper;

use Location\Location\Location;
use Symfony\Component\BrowserKit\HttpBrowser as Client;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;

class Translations
{
    const BASE_URL = 'https://en.wikipedia.org';

    const SUBDIVISION_BASE_URL = '/wiki/ISO_3166-2:';

    const TRANSLATION_DEST = '../../src/config/iso_3166-2.en-translations.json';

    const COUNTRIES_TO_SKIP = [
        'EE',  // For Estonia the local names are better than English ones
        'JP', // Source data from salsa-debian already has english translations where applicable
    ];

    protected Client $client;

    public function __construct()
    {
        $this->client = new Client(HttpClient::create());
    }

    public function load(): void
    {
        /** @var array<array{alpha_2: string, name: string}> $countries */
        $countries = (new Location())->getCountries();

        $translations = [];

        foreach ($countries as $key => $country) {
            if (in_array($key, self::COUNTRIES_TO_SKIP)) {
                continue;
            }

            $translations = [...$translations, ...$this->scrapeCountry($key)];
        }

        file_put_contents(self::TRANSLATION_DEST, json_encode($translations));
    }

    public function scrapeCountry(string $alphaCode): array
    {
        $url = sprintf('%s%s%s', self::BASE_URL, self::SUBDIVISION_BASE_URL, $alphaCode);

        $crawler = $this->client->request('GET', $url);

        $table = $crawler->filter('table.wikitable.sortable')->first();

        if (! $table->count()) {
            return [];
        }

        $trs = $table->filter('tr');

        $englishColumnNameIndex = '';
        $trs->first()->filter('th')->each(function (Crawler $th, $index) use (&$englishColumnNameIndex) {
            $text = mb_strtolower($this->filterText($th->text()));

            if (str_starts_with('subdivision name (en)', $text) ||
                str_starts_with('subdivision name (sv)', $text)
            ) {
                $englishColumnNameIndex = $index;
            }
        });

        $translations = [];

        if ($englishColumnNameIndex) {
            $total = $trs->count();

            for ($i = 1; $i < $total; $i++) {
                [$code, $name] = $this->scrapeRow($trs->eq($i), $englishColumnNameIndex);

                $translations[$code] = $name;
            }
        }

        return $translations;
    }

    private function filterText(string $th): string
    {
        $th = trim($th);

        return preg_replace('/\[note \d\]/', '', $th);
    }

    private function scrapeRow(Crawler $node, int $englishColumnNameIndex): array
    {
        $childNodes = $node->children();

        $code = $this->filterText($childNodes->first()->text());

        $name = $this->filterText($childNodes->eq($englishColumnNameIndex)->text());

        return [$code, $name];
    }
}
