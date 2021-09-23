<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

const WALL = "█";
const DIRTY = " ";
const CLEANED = ".";
const VISITED = "s";

const ROBOT = "▓";
const BRUSH = "░";
const CENTER ="X";

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file));
$grid = $ir->grid([" " => DIRTY, "x" => WALL]);

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . "duster"));
$robot = array_filter(array_map("array_filter", $ir->grid(["s" => ROBOT, "X" => CENTER, " " => false])));

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . "brushes"));
$brushes = array_filter(array_map("array_filter", $ir->grid(["k" => BRUSH, "X" => CENTER, " " => false])));

$dustie = []; // oh, beloved Dustie!
foreach ($robot as $cy => $row) {
    foreach ($row as $cx => $val) {
        if ($val == CENTER) {
            foreach ($robot as $y => $row) {
                foreach ($row as $x => $val) {
                    if ($val == ROBOT || $val == CENTER) {
                        $dustie[$y-$cy][$x-$cx] = ROBOT;
                    }
                }
            }
            break 2;
        }
    }
}

$body = $dustie;

foreach ($brushes as $cy => $row) {
    foreach ($row as $cx => $val) {
        if ($val == CENTER) {
            foreach ($brushes as $y => $row) {
                foreach ($row as $x => $val) {
                    if (($val == BRUSH || $val == CENTER) && !isset($dustie[$y-$cy][$x-$cx])) {
                        $dustie[$y-$cy][$x-$cx] = BRUSH;
                    }
                }
            }
            break 2;
        }
    }
}

$ymin = min(array_keys($grid));
$ymax = max(array_keys($grid));
$xmin = min(array_map(function($row) { return min(array_keys($row)); }, $grid));
$xmax = max(array_map(function($row) { return max(array_keys($row)); }, $grid));

$centerWidth = 1;
while (
    isset($dustie[0][$centerWidth]) && $dustie[0][$centerWidth] === ROBOT &&
    isset($dustie[0][-$centerWidth]) && $dustie[0][-$centerWidth] === ROBOT
) {
    $centerWidth++;
}
$centerWidth--;

for ($y = $ymin; $y <= $ymax; $y++) {
    for ($x = $xmin; $x <= $xmax; $x++) {
        if (isset($grid[$y][$x+$centerWidth]) && $grid[$y][$x+$centerWidth] === WALL) {
            //we won't match, and we can safely skip the whole width of Dustie's body
            $x += $centerWidth * 2;
            continue;
        }
        foreach ($body as $dY => $dRow) {
            foreach ($dRow as $dX => $type) {
                if ((!isset($grid[$y+$dY][$x+$dX]) || $grid[$y+$dY][$x+$dX] === WALL)) {
                    continue 3; // we crashed; start looking at next coordinate
                }
            }
        }

        foreach ($dustie as $dY => $dRow) {
            foreach ($dRow as $dX => $type) {
                if (isset($grid[$y+$dY][$x+$dX]) && $grid[$y+$dY][$x+$dX] === DIRTY) {
                    $grid[$y+$dY][$x+$dX] = CLEANED;
                }
            }
        }
    }
}

$sum = 0;
for ($y = $ymin; $y <= $ymax; $y++) {
    for ($x = $xmin; $x <= $xmax; $x++) {
        if (($grid[$y][$x] ?? false) === DIRTY) {
            $sum++;
        }
    }
}
echo $sum."\n";
#288903: CORRECT
