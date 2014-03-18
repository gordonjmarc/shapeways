<?php

//read contents of file

if(count($argv) < 3) {
	echo "usuage: arg1 [input file] arg2 [output file]\n";
	exit;
}

$inputFile = $argv[1];
$outputFile = $argv[2];

file_put_contents($outputFile, ''); //clear output file

$handle = fopen($inputFile, "r"); //read input file
$readInputFile = file_get_contents($inputFile);
$inputLines = explode("\n", $readInputFile);

$favoritesArr = array(); // all favorites key is integer index, value is favorite input from last.fm
$artistFrequencyMap = array(); // key is artist, value is array of favorite keys from previous array

// turn input into indexed integer indexed favorite array, composed of artists as an array
$i= 0;
foreach($inputLines as $key => $value) {
	$favoritesArr[$i] = explode(",", $value);
	$i++;
}

//create artist frequency array, this is  band name => integer index of favorite array it was encountered in
foreach($favoritesArr as $currentFavoritesKey => $currentFavoritesArr) {
	foreach($currentFavoritesArr as $artist) {
		if(array_key_exists($artist, $artistFrequencyMap)) {
			array_push($artistFrequencyMap[$artist] , $currentFavoritesKey );
		}
		else {
			$artistFrequencyMap[$artist] = array($currentFavoritesKey);
		}
	}
}

//remove artists who don't have 50 or more occurances in favorite arrays
foreach($artistFrequencyMap  as $artist => $frequency) {
	if(!(count($frequency) >= 50)) {
		unset($artistFrequencyMap[$artist]);
	}
}

//get all pairs of unique band pairs, which occur in 50 or more favorite lists
$artists = array_keys($artistFrequencyMap);
$pairsToCheck = array();

foreach($artists as $artist) {
	foreach($artists as $pairedArtist) {
		if($artist == $pairedArtist) {
			continue;
		}

		if($artist < $pairedArtist)  {
			$artistPairString = $artist . "," . $pairedArtist;
		} else {
			$artistPairString = $pairedArtist . "," . $artist;
		}

		$pairsToCheck[$artistPairString] = false;
	}
}

$pairsToCheck = array_keys($pairsToCheck); // we only wanted the band names, drop the key values


//walk artist pairs to check, if intersection of artists is equal or greater to 50, they meet the requirements, echo to the output file
foreach($pairsToCheck as $pairStr) {
	$pairStrs = explode(",",$pairStr);

	$artist1 = $pairStrs[0];
	$artist2 = $pairStrs[1];

	$intersect = array_intersect($artistFrequencyMap[$artist1] , $artistFrequencyMap[$artist2]);

	if( count($intersect) >= 50) {
		file_put_contents($outputFile , $pairStr. "\n", FILE_APPEND);

	}
}

?>