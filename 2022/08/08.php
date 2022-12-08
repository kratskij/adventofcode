<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$grid = $ir->intGrid();

$width = count($grid); // assumes quadratic shape

$visibles = [];
foreach ($grid as $y => $row) {
    foreach ($row as $x => $val) {
        $left = $right = $up = $down = true;
        $leftDistance = $x;
        $rightDistance = $width-$x-1;
        $upDistance = $y;
        $downDistance = $width-$y-1;
        for ($i = 0; $i < $width; $i++) {
            if ($i < $x && $grid[$y][$i] >= $val) {
                $left = false;
                $leftDistance = min($leftDistance, $x-$i);
            } else if ($i > $x && $grid[$y][$i] >= $val) {
                $right = false;
                $rightDistance = min($rightDistance, $i-$x);
            }
            if ($i < $y && $grid[$i][$x] >= $val) {
                $up = false;
                $upDistance = min($upDistance, $y-$i);
            } else if ($i > $y && $grid[$i][$x] >= $val) {
                $down = false;
                $downDistance = min($downDistance, $i-$y);
            }
        }
        if ($left || $right || $up || $down) {
            $visibles[$y."_".$x] = ($leftDistance * $rightDistance * $upDistance * $downDistance);
        }
    }
}

$p1 = count($visibles);
$p2 = max($visibles);

echo "P1: $p1\nP2: $p2\n";
