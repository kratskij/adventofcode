<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";

$file = ($test) ? "test" : "input";
$input = explode("\n", trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $file)));

foreach ($input as $k => $line) {
	$pos = ["n" => 0, "e" => 0] ;
	$max = 0;
    $p = explode(",", $line);
    foreach ($p as $char) {
    	switch($char) {
    		case "n":
    			$pos["n"]++; 
    			break;
    		case "s":
    			$pos["n"]--;
    			break;
    		case "ne":
    			$pos["n"] += .5;
    			$pos["e"] += .5;
    			break;
    		case "sw":
    			$pos["n"] -= .5;
    			$pos["e"] -= .5;
    			break;
    		case "nw":
    			$pos["n"] += .5;
    			$pos["e"] -= .5;
    			break;
    		case "se":
    			$pos["n"] -= .5;
    			$pos["e"] += .5;
    			break;
    	}
		$max = max($max, distance($pos));
    }
}

echo "Part 1: " . distance($pos) . "\n";
echo "Part 2: " . $max . "\n";

function distance($pos)
{
	return ceil(abs($pos["n"]) + abs($pos["e"]));
}

#echo $sum;
#echo $outString;
