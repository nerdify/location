<?php

namespace Location\Location\Task;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Utils;
use Location\Location\Utils\JsonCollectionStreamWriter;

class UpdateGeoNameBack
{
    const ALL_COUNTRIES_SRC = 'https://download.geonames.org/export/dump/allCountries.zip';

    const ALL_COUNTRIES_DEST = '../../src/config/geonames.json';

//    const ALL_COUNTRIES_DEST = "../../src/config/geonames.txt";
    const ZIP_TEMP_NAME = 'allCountries.zip';

    const ZIP_TEMP_EXTRACT_DEST = 'countries';

    /**
     * @throws GuzzleException
     */
    public function load(): void
    {
        $client = new Client();

        $path = sprintf('%s/%s', sys_get_temp_dir(), self::ZIP_TEMP_NAME);
        $zipPath = sprintf('%s/%s', sys_get_temp_dir(), self::ZIP_TEMP_EXTRACT_DEST);

        $resource = Utils::tryFopen($path, 'w');

        var_dump($path);
        $client->request('GET', self::ALL_COUNTRIES_SRC, ['sink' => $resource]);

        $zip = new \ZipArchive();

        if ($zip->open($path)) {
            $zip->extractTo($zipPath);
            $zip->close();

            echo "\e[0;32;mUnzipped Process Successful!\e[0m\n";

            unlink($path);
        } else {
            echo "cannot open file zip\n";
        }

        $zipFileName = sprintf('%s/%s', $zipPath, 'allCountries.txt');

        $this->processGeoNameFile($zipFileName);

        unlink($zipFileName);
    }

    private function processGeoNameFile(string $filePath): void
    {
        echo "\e[0;32;mReading file...\e[0m\n";

        $writer = new JsonCollectionStreamWriter(self::ALL_COUNTRIES_DEST, false);

        $fileHandle = fopen($filePath, 'r');

        foreach ($this->getAllLines($fileHandle) as $line) {
            $values = explode("\t", $line, 10);
            if (count($values) > 9) {
                [$id, $name, $_, $_, $_, $_, $featureClass, $_, $countryCode, $_] = $values;

                // feature classes defined here: http://download.geonames.org/export/dump
                if (in_array($featureClass, ['P', 'A'])) {
                    $geoNames[$id] = ['name' => $name];

                    $writer->push($geoNames);
                }
            }
        }

        fclose($fileHandle);

        echo "\e[0;32;mWriting in a new File...\e[0m\n";

        $writer->close();

        echo "\e[0;32;mThe data has been written...\e[0m\n";
    }

    public function getAllLines($fileHandle): \Generator
    {
        while (! feof($fileHandle)) {
            yield fgets($fileHandle);
        }
    }
}

//
//public function processGeoNameFile(string $filePath): void
//{
//    echo "\e[0;32;mReading file...\e[0m\n";
//
//    $writer = new JsonCollectionStreamWriter(self::ALL_COUNTRIES_DEST);
//
//    $fileHandle = fopen($filePath, 'r');
////        $geoNames = [];
////        $geoNames = '';
//
////        $gestor = @fopen("allCountries.txt", "r");
//
////        if ($fileHandle) {
////            while (($line = fgets($fileHandle, 4096)) !== false) {
////                $values = explode("\t", $line, 10);
////                if (count($values) > 9) {
////                    [$id, $name, $_, $_, $_, $_, $featureClass, $_, $countryCode, $_] = $values;
////
////                    // feature classes defined here: http://download.geonames.org/export/dump
////                    if (in_array($featureClass, ['P', 'A'])) {
//////                        $geoNames[] = compact('id', 'name', 'countryCode');
////                        $geoNames .= "$id\t$name\t$countryCode\n";
////                    }
////                }
////            }
////
////            if (! feof($fileHandle)) {
////                echo "Error: fallo inesperado de fgets()\n";
////            }
////        }
//
//    foreach ($this->getAllLines($fileHandle) as $line) {
//        $values = explode("\t", $line, 10);
//        if (count($values) > 9) {
//            [$id, $name, $_, $_, $_, $_, $featureClass, $_, $countryCode, $_] = $values;
//
//            // feature classes defined here: http://download.geonames.org/export/dump
//            if (in_array($featureClass, ['P', 'A'])) {
//                $geoNames = compact('id', 'name', 'countryCode');
//
//                $writer->push($geoNames);
//            }
//        }
//
////            [$id, $name, $_, $_, $_, $_, $featureClass, $_, $countryCode, $_] = explode("\t", $line);
////
////            // feature classes defined here: http://download.geonames.org/export/dump
////            if (in_array($featureClass, ['P', 'A'])) {
////                $geoNames[] = compact('id', 'name', 'countryCode');
////            }
//    }
//
//    fclose($fileHandle);
//
//    echo "\e[0;32;mWriting in a new File...\e[0m\n";
//
//    $writer->close();
////        file_put_contents(self::ALL_COUNTRIES_DEST, json_encode($geoNames));
////        file_put_contents(self::ALL_COUNTRIES_DEST, $geoNames);
//
//    echo "\e[0;32;mThe data has been written...\e[0m\n";
//}
