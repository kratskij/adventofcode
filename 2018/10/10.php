<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once(__DIR__."/../inputReader.php");

$ir = new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file);
$input = $ir->regex("position=\<\s*([\d\-]+),\s*([\d\-]+)\>\svelocity=\<\s*([\d\-]+),\s*([\d\-]+)\>");

$area = $prevArea = INF;
$seconds = 0;
while ($area == INF || $area < $prevArea) {
    $seconds++;
    foreach ($input as $k => &$line) {
        $line[0] += $line[2];
        $line[1] += $line[3];
    }
    $xs = array_map(function($line) { return $line[0] + $line[2]; }, $input);
    $ys = array_map(function($line) { return $line[1] + $line[2]; }, $input);

    $prevArea = $area;
    $area = (max($xs)-min($xs)) * (max($ys)-min($ys));
}
$positions = [];
foreach ($input as $line) {
    $positions[$line[1] - $line[3]][$line[0] - $line[2]] = true;
    $xs[$line[0] + $line[2]] = $line[0] + $line[2];
}

$ys = array_keys($positions);
echo "Part 1:\n";
for ($y = min($ys); $y <= max($ys); $y++) {
    for ($x = min($xs); $x <= max($xs); $x++) {
        echo (isset($positions[$y][$x])) ? "█" : " ";
    }
    echo "\n";
}

echo "Part 2: $seconds\n";
