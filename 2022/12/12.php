<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$grid = $ir->grid();
$animate = false;

$goalX = $goalY = false;
$queues = [ 1 => [], 2 => [] ];
$visiteds = [ 1 => [], 2 => [] ];

$charGrid = $grid;
foreach ($grid as $y => $row) {
    foreach ($row as $x => $val) {
        if ($val === "S") {
            $queues[1][] = [$y, $x, 0, [$y => [$x => true]]];
            $visiteds[1][$y][$x] = true;
            $grid[$y][$x] = "a";
        } else if ($val === "E") {
            $goalX = $x;
            $goalY = $y;
            $grid[$y][$x] = "z";
        }
        if ($grid[$y][$x] == "a") {
            $queues[2][] = [$y, $x, 0, [$y => [$x => true]]];
            $visiteds[2][$y][$x] = true;
        }
        $grid[$y][$x] = ord($grid[$y][$x]) - 96;
    }
}

$dirs = [ [0,1], [1,0], [0,-1], [-1,0] ];

$results = [];
foreach ($queues as $part => $queue) {
    $visited = $visiteds[$part];
    while ($n = array_shift($queue)) {
        list($currentY, $currentX, $score, $trace) = $n;

        if ($currentY == $goalY && $currentX == $goalX) {
            $results[$part] = $score;
            break;
        }
        foreach ($dirs as $dir) {
            list($dy, $dx) = $dir;
            $newY = $currentY + $dy;
            $newX = $currentX + $dx;
            if (
                isset($grid[$newY][$newX]) &&
                !isset($visited[$newY][$newX]) &&
                $grid[$newY][$newX] - $grid[$currentY][$currentX] <= 1 // increase > 1 is too steep
            ) {
                $visited[$newY][$newX] = true;
                $newTrace = $trace;
                $newTrace[$newY][$newX] = true;
                $queue[] = [$newY, $newX, $score + 1, $newTrace];
                $color  = 32;
            } else {
                $color = 31;
            }

            if ($animate) {
                system("clear");
                printGrid($grid, $newY, $newX, $color, $trace, $charGrid);
                usleep(1000);
            }
            #sleep(1);
        }
    }
}
$p1 = $results[1];
$p2 = $results[2];

echo "P1: $p1\nP2: $p2\n";

function printGrid($grid, $curY, $curX, $color, $trace, $charGrid) {
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
            if ($y == $curY && $x == $curX) {
                $out .= colorize($charGrid[$y][$x], $color);
            } else if (isset($grid[$y][$x])) {
                if (isset($trace[$y][$x])){
                    $out .= $charGrid[$y][$x];
                } else {
                    $out .= " ";
                }
            }
        }
        $out .= "\n";
    }

    echo $out;
}
function colorize($string, $color) {
    return sprintf("\033[%sm%s\033[0m", $color, $string);
}
