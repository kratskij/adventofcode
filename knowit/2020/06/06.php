<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = $ir->explode(",");

$elves = ($test) ? 9 : 127;

rsort($input);
$max = $candies = $packages = 0;
foreach ($input as $i) {
    $packages++;
    $candies += $i;

    if (($candies % $elves) == 0) {
        $max = max($max, $candies / $elves);
        $maxAt = $packages;
    }
}
echo "$max,$maxAt\n";
