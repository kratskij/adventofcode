<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");
require_once(__DIR__."/../Util.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = $ir->csv(" -> ");

const WALL = 1;
const SAND = 2;

$p1 = $p2 = 0;
$grid = [];
foreach ($input as $k => $line) {
    $prevY = $prevX = false;
    foreach ($line as $pos) {
        list($x, $y) = array_map("intval", explode(",", $pos));
        $grid[$y][$x] = WALL;
        if ($prevY !== false) {
            for ($ny = min($y, $prevY); $ny <= max($y, $prevY); $ny++) {
                for ($nx = min($x, $prevX); $nx <= max($x, $prevX); $nx++) {
                    $grid[$ny][$nx] = WALL;
                }
            }
        }
        $prevY = $y;
        $prevX = $x;
    }
}

$startY = 0;
$startX = 500;

$settled = 0;
$maxY = max(array_keys($grid));

while (true) {
    $y = $startY;
    $x = $startX;
    while ($y < $maxY + 1) {
        if (!isset($grid[$y+1]) || !isset($grid[$y+1][$x])) {
            // nothing below us; do nothing/fall further
            if (!$p1 && $y+1 > $maxY) {
                // we're spilling below the lowest point for the first time; p1 is done.
                $p1 = $settled;
            }
        } else if (!isset($grid[$y+1][$x-1])) {
            $x--;
        } else if (!isset($grid[$y+1][$x+1])) {
            $x++;
        } else if (isset($grid[$y+1][$x])) {
            $stable = true;
            // we found stable groud; settle here
            break;
        }
        $y++;
    }

    $grid[$y][$x] = SAND;
    $settled++;

    if ($y == $startY && $x == $startX) {
        break;
    }
}
$p2 = $settled;

echo "P1: $p1\nP2: $p2\n";
