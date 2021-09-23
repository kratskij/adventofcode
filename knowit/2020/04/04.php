<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = $ir->csv(", ");

$recipe = [ "sukker" => 2, "mel" => 3, "melk" => 3, "egg" => 1 ];

$delivered = array_fill_keys(array_keys($recipe), 0);
foreach ($input as $k => $line) {
    foreach ($line as $l) {
        list($unit, $units) = explode(":", $l);
        $delivered[$unit] += $units;
    }
}

$min = PHP_INT_MAX;
foreach ($recipe as $unit => $units) {
    $min = min($min, floor($delivered[$unit] / $units));
}
echo $min."\n";
