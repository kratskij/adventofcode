<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";

$file = ($test) ? "test" : "input";
$input = explode("\n", trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $file)));
#$input = str_split($input[0]);
#$input = preg_match ("//" , $input, &$matches)
#var_dump($input);

$regex = "//";
$values = [];
$outString = "";
$sum = 0;
$c = 0;

foreach ($input as $k => $line) { #$matches?
    #$p = preg_split("/\s+/", $line);
	#preg_match($regex, $row, $matches);
	#list(, $turn, $length) = $matches; #var_dump($matches);
    #echo $line;
}

#echo $sum;
#echo $outString;
