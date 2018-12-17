<?php

ini_set('memory_limit','2048M');

define("WALL_HIT",         1);
define("OUT_OF_RESERVOIR", 2);

define("FLOW",  "💧");
define("FLOW_TOP", "◠");
define("STILL", "▒");
define("CLAY",  "▓");
define("SAND",  " ");


define("LEFT", -1);
define("RIGHT", 1);

require_once(__DIR__."/../inputReader.php");

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";



$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = $ir->regex("^(\w)\=([\d\-]+)\,\s\w\=([\d\-]+)\.\.([\d\-]+)$");

$grid = [];
foreach ($input as $k => $line) {
    list($first, $val1, $from, $to) = $line;
    for ($i = $from; $i <= $to; $i++) {
        $grid[($first == "y") ? $val1 : $i][ ($first == "y") ? $i : $val1] = CLAY;
    }
}

$minY = min(array_keys($grid));
$maxY = max(array_keys($grid));



$streamQueue = [ [$minY,500] ];
while (list($y, $x) = array_shift($streamQueue)) {
    if (!fall($grid, $y, $x, $maxY)) { // we reached the bottom!
        continue;
    }

    if ($grid[$y][$x] == FLOW_TOP) { // we landed in water! gtfo
        continue;
    }

    foreach (fillReservoir($grid, $y, $x) as $stream) {
        $streamQueue[] = $stream;
    }
}



$still = $flow = 0;
foreach ($grid as $row) {
    foreach ($row as $g) {
        if ($g == STILL) {
            $still++;
        } else if ($g == FLOW || $g == FLOW_TOP) {
            $flow++;
        }
    }
}

printGrid($grid, $minY, $maxY);
echo "Part 1: " . ($still + $flow) . "\n";
echo "Part 2: $still\n";



function fall(&$grid, &$y, $x, $stop) {
    while (!isset($grid[$y][$x])) {
        $grid[$y][$x] = FLOW;
        if (++$y > $stop) { // We reached the bottom
            return false;
        }
    }
    return true;
}

function fillSideways(&$grid, $y, $x, $direction, &$streams) {
    while (true) {
        if (isset($grid[$y][$x]) && $grid[$y][$x] == CLAY) { // we hit a wall
            return WALL_HIT;
        } else if (@$grid[$y+1][$x] == FLOW) { //we hit a stream! Annoying bug
            return OUT_OF_RESERVOIR;
        } else {
            $grid[$y][$x] = STILL;
            if (!isset($grid[$y+1][$x])) { // we hit an edge! create new filler?
                $streams[] = [$y+1, $x, ];
                return OUT_OF_RESERVOIR;
            }
        }
        $x += $direction;
    }
}

function fillReservoir(&$grid, $y, $x) {
    //fill to both sides until we're out of the reservoir. return new stream(s)
    $streams = [];
    while (true) {
        $left = fillSideWays($grid, $y, $x, LEFT, $streams);
        $right = fillSideWays($grid, $y, $x, RIGHT, $streams);

        if ($left == WALL_HIT && $right == WALL_HIT) {
            //wall on both sides; step up, start at same position
            $y--;
            $left = $right = false;
        } else if (
            ($left == WALL_HIT && $right == OUT_OF_RESERVOIR) ||
            ($left == OUT_OF_RESERVOIR && $right == WALL_HIT) ||
            ($left == OUT_OF_RESERVOIR && $right == OUT_OF_RESERVOIR)
        ) {
            // We're done in this bucket. Fill the top layer with pipes, for some reason
            $origX = $x;
            while (isset($grid[$y][$x]) && $grid[$y][$x] == STILL) {
                $grid[$y][$x] = FLOW_TOP;
                $x--;
            }
            $x = $origX + 1;
            while (isset($grid[$y][$x]) && $grid[$y][$x] == STILL) {
                $grid[$y][$x] = FLOW_TOP;
                $x++;
            }
            break;
        }

    }

    return $streams;
}

function printGrid($grid, $from, $to, $sleep = 0) {
    // system("clear");
    $maxX = -INF;
    $minX = INF;
    foreach ($grid as $g) {
        $maxX = max($maxX, max(array_keys($g)));
        $minX = min($minX, min(array_keys($g)));
    }

    for ($y = $from; $y <= $to; $y++) {
        echo str_pad($y, 5, " ", STR_PAD_RIGHT);
        for ($x = $minX; $x <= $maxX; $x++) {
            if (isset($grid[$y][$x])) {
                #echo "\033[1;32m";
            }
            echo isset($grid[$y][$x]) ? $grid[$y][$x] : SAND;
            if (isset($filled[$y."_".$x])) {
                #echo "\033[0m";
            }
        }
        echo "\n";
    }
    echo "\n";
    if ($sleep) {
        usleep($sleep);
    }
}
