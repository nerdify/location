<?php

use Location\Location\Location;
use Location\Location\Task\Scrapper\Translations;
use Location\Location\Task\UpdateGeoName;
use Location\Location\Task\UpdateIsoData;

require '/Users/carlosduarte/Documents/projects/nerdify/location-php/vendor/autoload.php';

dump(sys_get_temp_dir());
echo "\e[0;32;mExecuting task...\e[0m\n";

//echo "\e[0;32;mExecuting UpdateIsoData loadCountries task...\e[0m\n";
//(new UpdateIsoData())->loadCountries();
//sleep(1);
//
//echo "\e[0;32;mExecuting UpdateIsoData loadSubdivisions task...\e[0m\n";
//(new UpdateIsoData())->loadSubdivisions();
//sleep(1);
////
//echo "\e[0;32;mExecuting Translations task...\e[0m\n";
//(new Translations())->load();
//sleep(1);
//
echo "\e[0;32;mExecuting UpdateGeoName task...\e[0m\n";
(new \Location\Location\Task\UpdateGeoName())->load();
sleep(1);

echo "\e[0;32;mAll tasks completed...\e[0m\n";

