<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");
require_once(__DIR__."/../Util.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = $ir->csv(" -> ");
$p1 = $p2 = 0;
$grid = [];

const WALL = 1;
const SAND = 2;

foreach ($input as $k => $line) {
    $prevY = $prevX = false;
    foreach ($line as $pos) {
        list($x, $y) = array_map("intval", explode(",", $pos));
        $grid[$y][$x] = WALL;
        if ($prevY) {
            $dx = 0; $dy = 0;
            while ($prevY+$dy !== $y || $prevX+$dx !== $x) {
                if ($y > $prevY) {
                    $dy++;
                } else if ($y < $prevY) {
                    $dy--;
                }
                if ($x > $prevX) {
                    $dx++;
                } else if ($x < $prevX) {
                    $dx--;
                }
                if (!isset($grid[$prevY+$dy])) {
                    $grid[$prevY+$dy] = [];
                }
                $grid[$prevY+$dy][$prevX+$dx] = WALL;
            }
        }
        $prevY = $y;
        $prevX = $x;
    }
}

$i = 0;
$maxY = max(array_keys($grid));
$countp1 = true;

while (true) {
    $sandX = 500;
    $sandY = 0;
    while (
        $sandY < $maxY + 1 && (
            !isset($grid[$sandY+1]) ||
            !isset($grid[$sandY+1][$sandX]) ||
            !isset($grid[$sandY+1][$sandX-1]) ||
            !isset($grid[$sandY+1][$sandX+1])
        )
    ) {
        if (!isset($grid[$sandY+1]) || !isset($grid[$sandY+1][$sandX])) {
            if ($sandY+1 > $maxY) {
                $countp1 = false;
            }
            $sandY++;
            continue;
        }

        if (!isset($grid[$sandY+1][$sandX-1])) {
            $sandX--;
            $sandY++;
            continue;
        } else if (!isset($grid[$sandY+1][$sandX+1])) {
            $sandX++;
            $sandY++;
            continue;
        } else if (isset($grid[$sandY+1][$sandX])) {
            break;
        }
        $sandY++;
        if ($sandY > $maxY) {
            break 2;
        }
    }

    $grid[$sandY][$sandX] = SAND;
    if ($countp1) {
        $p1++;
    }
    $p2++;

    if ($sandY == 0 && $sandX == 500) {
        break;
    }
}

echo "P1: $p1\nP2: $p2\n";

#657
