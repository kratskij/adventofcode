<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

$serialNumber = ($test) ? 8 : 6042;

$width = 300;

$grid = [];
for ($y = 1; $y < $width; $y++) {
    for ($x = 1; $x < $width; $x++) {
        $id = $x + 10;
        $grid[$y][$x] = substr((($id * $y) + $serialNumber) * $id, -3, 1) - 5;
    }
}

$maxSum = -INF;
$topPos = false;

for ($y = 1; $y < $width - 2; $y++) {
    for ($x = 1; $x < $width - 2; $x++) {
        $sum = $grid[$y][$x] +
            $grid[$y][$x+1] +
            $grid[$y][$x+2] +
            $grid[$y+1][$x] +
            $grid[$y+1][$x+1] +
            $grid[$y+1][$x+2] +
            $grid[$y+2][$x] +
            $grid[$y+2][$x+1] +
            $grid[$y+2][$x+2];
            if ($sum > $maxSum) {
                $topPos = [$x, $y];
                $maxSum = $sum;
            }
    }
}
echo "Part 1: " . implode(",", $topPos) . "\n";

$maxSum = -INF;
$topPos = false;
for ($y = 1; $y < $width; $y++) {
    $sum = 0;
    for ($x = 1; $x < $width; $x++) {
        $sum = $grid[$y][$x];
        for ($size = 1; $size < $width-max($x, $y); $size++) {
            for ($yAdd = 0; $yAdd <= $size; $yAdd++) {
                $sum += $grid[$y + $yAdd][$x + $size];
            }
            for ($xAdd = 0; $xAdd < $size; $xAdd++) {
                $sum += $grid[$y + $size][$x + $xAdd];
            }

            if ($sum > $maxSum) {
                $topPos = [$x, $y, $size+1];
                $maxSum = $sum;
            }
        }
    }
}
echo "Part 2: " . implode(",", $topPos) . "\n";
