<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once(__DIR__."/../inputReader.php");

$ir = new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file);
$input = $ir->regex("#(\d+)\s\@\s(\d+),(\d+)\:\s(\d+)x(\d+)");
$area = [];
$sum = 0;
foreach ($input as $k => $i) {
    list($id, $leftOffset, $topOffset, $width, $height) = $i;
    for ($x = $leftOffset; $x < $leftOffset + $width; $x++) {
        for ($y = $topOffset; $y < $topOffset + $height; $y++) {
            if (!isset($area[$x][$y])) {
                $area[$x][$y] = 0;
            }
            $area[$x][$y]++;
            if ($area[$x][$y] == 2) {
                $sum++;
            }
        }
    }
}

echo "Part 1: $sum\n";

$min = [INF, null];
foreach ($input as $k => $i) {
    list($id, $leftOffset, $topOffset, $width, $height) = $i;
    $max = 0;
    for ($x = $leftOffset; $x < $leftOffset + $width; $x++) {
        for ($y = $topOffset; $y < $topOffset + $height; $y++) {
            $max = max($max, $area[$x][$y]);
        }
    }
    if ($max < $min[0]) {
        $min = [$max, $id];
    }
}

echo "Part 2: {$min[1]}\n";
