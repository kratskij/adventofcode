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
            $visiblesGrid[$y][$x] = true;
        }
    }
}

#printGrid($grid, $visiblesGrid);

$p1 = count($visibles);
$p2 = max($visibles);

echo "P1: $p1\nP2: $p2\n";


function printGrid($grid, $visiblesGrid) {
    $intMap = [
        0 => " ",
        1 => "_",
        2 => "▁",
        3 => "▂",
        4 => "▃",
        5 => "▄",
        6 => "▅",
        7 => "▆",
        8 => "▇",
        9 => "█",
    ];

    $minY = $minX = PHP_INT_MAX;
    $maxY = $maxX = -PHP_INT_MAX;
    foreach ($grid as $y => $row) {
        $minY = min($minY, $y);
        $maxY = max($maxY, $y);
        $minX = min($minX, min(array_keys($row)));
        $maxX = max($maxX, max(array_keys($row)));
    }

    $out = "";
    for ($y = $minY; $y <= $maxY; $y++) {
        for ($x = $minX; $x <= $maxX; $x++) {
            $out .= colorize($intMap[$grid[$y][$x]], (isset($visiblesGrid[$y][$x])) ? 37 : 90);
        }
        $out .= "\n";
    }

    echo "$out\n";
}

function colorize($string, $color) {
    return sprintf("\033[%sm%s\033[0m", $color, $string);
}
