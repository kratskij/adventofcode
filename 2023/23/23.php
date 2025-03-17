<?php
#runtime: 231m
ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");
#require_once __DIR__."/../Util.php";

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim();
$grid = $ir->grid();

const PATH = ".";
const WALL = "#";

const SLOPE_SOUTH = "v";
const SLOPE_EAST = ">";
const SLOPE_WEST = "<";
const SLOPE_NORTH = "^";

const NORTH = [ -1,  0 ];
const EAST  = [  0,  1 ];
const SOUTH = [  1,  0 ];
const WEST  = [  0, -1 ];

$starty = 0;
$startx = array_search(PATH, $grid[$starty]);
$endy = count($grid) - 1;
$endx = array_search(PATH, $grid[$endy]);


$p1 = walk($grid, $starty, $startx, $endy, $endx, true);
$p2 = walk($grid, $starty, $startx, $endy, $endx, false);
echo "P1: $p1\nP2: $p2\n";

function walk($grid, $starty, $startx, $endy, $endx, $slipperySlopes) {
    $noSlopeGrid = [];
    // convert all slopes to paths
    foreach ($grid as $y => $row) {
        foreach ($row as $x => $val) {
            if (in_array($val, [SLOPE_NORTH, SLOPE_EAST, SLOPE_SOUTH, SLOPE_WEST])) {
                $noSlopeGrid[$y][$x] = PATH;
            } else {
                $noSlopeGrid[$y][$x] = $val;
            }
        }
    }
    if (!$slipperySlopes) {
        $grid = $noSlopeGrid;
    }

    $dirs = [NORTH, EAST, SOUTH, WEST];

    //let's find the last junction, as we MUST select the exit route from there:
    $q = [ [$endy, $endx, false] ];
    $v = [];
    while ($c = array_shift($q)) {
        [$y, $x, $lastDir] = $c;
        if (isset($v[$y][$x])) {
            continue;
        }
        $v[$y][$x] = true;
        $exits = 0;
        foreach ($dirs as $dir) {
            [$yd, $xd] = $dir;
            $yy = $y+$yd;
            $xx = $x+$xd;
            if (($noSlopeGrid[$yy][$xx] ?? WALL) == PATH) {
                $exits++;
                $q[] = [$yy, $xx, $dir];
            }
        }
        if ($exits > 2) {
            if (!isset($lastJunctionY)) {
                $lastJunctionY = $y;
                $lastJunctionX = $x;
                $lastJunctionDir = [$lastDir[0] * -1, $lastDir[1] * -1];
            }
            $junctions[$y][$x] = $exits;
        }
    }

    $q = [ [$starty, $startx, [], 0] ];
    $maxDst = 0;
    $l = $v = [];
    while ($c = array_pop($q)) {
        [$y, $x, $v, $dst] = $c;
        if ($y == $endy && $x == $endx) {
            if ($dst > $maxDst) {
                echo "New end at $dst, max end is $maxDst, queue is " . count($q) . "\n";
            }
            $maxDst = max($maxDst, $dst);
            continue;
        }

        if (isset($junctions[$y][$x])) {
            $idx = "";
            foreach ($dirs as $dirIdx => $dir) {
                [$yd,$xd] = $dir;
                $yy = $y+$yd;
                $xx = $x+$xd;
                if ($noSlopeGrid[$yy][$xx] ?? WALL == PATH) {
                    $idx .= isset($v[$yy][$xx]) ? "1" : "0";
                }
            }
            if (isset($l[$y][$x][$idx]) && $dst == $l[$y][$x][$idx]) {
                echo "junction at $y,$x, idx = $idx\n";
                continue;
            }
            $l[$y][$x][$idx] = $dst;
        }
        $v[$y][$x] = true;
        foreach ($dirs as $dir) {
            [$yd,$xd] = $dir;
            $yy = $y+$yd;
            $xx = $x+$xd;
            if (isset($v[$yy][$xx]) || !isset($grid[$yy][$xx])) {
                continue;
            }
            if (
                ($grid[$yy][$xx] == PATH) ||
                ($dir == NORTH && ($grid[$yy][$xx] == SLOPE_NORTH)) ||
                ($dir == EAST && ($grid[$yy][$xx] == SLOPE_EAST)) ||
                ($dir == SOUTH && ($grid[$yy][$xx] == SLOPE_SOUTH)) ||
                ($dir == WEST && ($grid[$yy][$xx] == SLOPE_WEST))
            ) {
                if ($yy == $lastJunctionY && $xx == $lastJunctionX && $dir != $lastJunctionDir) {
                    continue;
                }
                $q[] = [$yy, $xx, $v, $dst + 1];
            }
        }
    }
    return $maxDst;
}


