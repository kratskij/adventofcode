<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$grid = $ir->intGrid();

$width = count($grid);

$visibles = [];
foreach ($grid as $y => $row) {
    foreach ($row as $x => $val) {
        $left = $right = $up = $down = true;
        $minLeft = $x;
        $minRight = $width-$x-1;
        $minUp = $y;
        $minDown = $width-$y-1;
        for ($i = 0; $i < $width; $i++) {
            if ($i < $x && $grid[$y][$i] >= $val) {
                $left = false;
                $minLeft = min($minLeft, $x-$i);
            }
            if ($i > $x && $grid[$y][$i] >= $val) {
                $right = false;
                $minRight = min($minRight, $i-$x);
            }
            if ($i < $y && $grid[$i][$x] >= $val) {
                $up = false;
                $minUp = min($minUp, $y-$i);
            }
            if ($i > $y && $grid[$i][$x] >= $val) {
                $down = false;
                $minDown = min($minDown, $i-$y);
            }
        }
        if ($left || $right || $up || $down) {
            $visibles[$y."_".$x] = ($minLeft * $minRight * $minUp * $minDown);
        }
    }
}

$p1 = count($visibles);
$p2 = max($visibles);

echo "P1: $p1\nP2: $p2\n";
