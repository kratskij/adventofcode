<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");
require_once __DIR__."/../Util.php";

$p1 = $p2 = 0;

const NS = "|";
const EW = "-";
const NE = "L";
const NW = "J";
const SW = "7";
const SE = "F";
const GROUND = ".";
const STARTING_POSITION = "S";

const WEST  = [ 0, -1];
const EAST  = [ 0,  1];
const NORTH = [-1,  0];
const SOUTH = [ 1,  0];

const DIRS = [ WEST, EAST, NORTH, SOUTH ];

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$grid = $ir->grid();

// let's add some padding, to easily traverse for an "outside" for p2
$rows = count($grid);
$cols = count($grid[0]);
for ($i = -1; $i <= $rows; $i++) {
    $grid[$i][-1] = GROUND;
    $grid[$i][$cols] = GROUND;
}
for ($i = -1; $i < $cols; $i++) {
    $grid[-1][$i] = GROUND;
    $grid[$rows][$i] = GROUND;
}

// let's double the size of the grid, to be able to traverse between narrow parts
$dblGrid = [];
foreach ($grid as $y => $row) {
    foreach ($row as $x => $val) {
        $dblGrid[$y * 2][$x * 2] = $val;
        if ($val == STARTING_POSITION) {
            $sY = $y * 2;
            $sX = $x * 2;
            $dblGrid[$y * 2 + 1][$x * 2] = NS;
            $dblGrid[$y * 2]    [$x * 2 + 1] = EW;
            $dblGrid[$y * 2 + 1][$x * 2 + 1] = GROUND;
        } else {
            $dblGrid[$y * 2 + 1][$x * 2] = (in_array($val, [NS, SE, SW])) ? NS : GROUND;
            $dblGrid[$y * 2]    [$x * 2 + 1] = (in_array($val, [EW, NE, SE])) ? EW : GROUND;
            $dblGrid[$y * 2 + 1][$x * 2 + 1] = GROUND;
        }
    }
}

// P1: Let's traverse the pipe system, and define the shortest distance to every node we can access
$q = [ [$sY, $sX, 0] ];
$visited = [];
while ($curr = array_pop($q)) {
    [$y, $x, $count] = $curr;

    if (isset($visited[$y][$x]) && $visited[$y][$x] < $count) {
        continue;
    }
    $visited[$y][$x] = $count;
    foreach (DIRS as $dir) {
        if ([$newY, $newX] = isValid($dblGrid, $y, $x, $dir)) {
            // We've doubled the size of the array, so every step should be counted as a half step
            $q[] = [$newY, $newX, $count + .5];
        }
    }
}

$p1 = max(array_map("max", $visited));

// Lets start at a point we _know_ is outside (e.g. <-1,0>), and traverse everything we can find from there
$q = [ [-1, 0] ];
$outside = [];
while ($curr = array_pop($q)) {
    [$y, $x] = $curr;
    if (isset($outside[$y][$x])) {
        continue;
    }
    $fromSymbol = $dblGrid[$y][$x];
    $outside[$y][$x] = true;
    foreach (DIRS as $dir) {
        [$dy,$dx] = $dir;
        if (isset($dblGrid[$y + $dy][$x + $dx]) && !isset($visited[$y][$x])) {
            $q[] = [$y + $dy, $x + $dx];
        }
    }
}

// P2: We can now find all nodes which are inside by traversing the full grid, and subtracting what we visited in P1 and what we know is outside
foreach ($dblGrid as $y => $row) {
    foreach ($row as $x => $val) {
        if (
            ($y % 2) == 0 && ($x % 2) == 0 && // <-- don't count the in-between-nodes we added
            !isset($visited[$y][$x]) && !isset($outside[$y][$x])
        ) {
            $p2++;
        }
    }
}
function isValid($grid, $y, $x, $dir) {
    [$dy, $dx] = $dir;
    if (!isset($grid[$y][$x]) || !isset($grid[$y+$dy][$x+$dx])) {
        return false;
    }

    $fromSymbol = $grid[$y][$x];
    $toSymbol = $grid[$y+$dy][$x+$dx] ?? false;
    $validTo = ($dir == NORTH && in_array($toSymbol, [NS, SW, SE])) ||
        ($dir == SOUTH && in_array($toSymbol, [NS, NW, NE])) ||
        ($dir == EAST && in_array($toSymbol, [EW, NW, SW])) ||
        ($dir == WEST && in_array($toSymbol, [EW, NE, SE]));

    $validFrom =
        $fromSymbol == STARTING_POSITION || 
        ($dir == NORTH && in_array($fromSymbol, [NS, NE, NW])) ||
        ($dir == SOUTH && in_array($fromSymbol, [NS, SE, SW])) ||
        ($dir == EAST && in_array($fromSymbol, [EW, NE, SE])) ||
        ($dir == WEST && in_array($fromSymbol, [EW, NW, SW]));

    return ($validFrom && $validTo) ? [ $y + $dy, $x + $dx] : false;
}

echo "P1: $p1\nP2: $p2\n";
