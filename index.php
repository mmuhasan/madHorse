<?php

require_once "madhorse/class.autoload.inc";
$objMadHorseAutoLoad = new \madHorse\autoLoad();
$objMadHorseAutoLoad->register();

$objMadHorseFactory = new \madHorse\factory();

$madHorse = $objMadHorseFactory->buildSkeleton($objMadHorseAutoLoad,$objMadHorseFactory);

/**
* Create basic objects
* Create userInput objects
* Create routers
*/
$madHorse->growUp();

/**
* Load App specific objects
* Update Madhorse with app specific settings
* update autoloader
* load apps
*/
$madHorse->inSeason();

/**
* run app.
* compile views
* dispach output
*/
$madHorse->breed();