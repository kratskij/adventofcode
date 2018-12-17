<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = $ir->regex("^(\w)\=([\d\-]+)\,\s(\w)\=([\d\-]+)\.\.([\d\-]+)$");

$grid = [];
foreach ($input as $k => $line) {
    list($y, $val1, $x, $from, $to) = $line;
    for ($i = $from; $i <= $to; $i++) {
        if ($y == "x") {
            $grid[$i][$val1] = "#";
        } else if ($y == "y") {
            $grid[$val1][$i] = "#";
        }
    }
}

$minY = min(array_keys($grid));
$maxY = max(array_keys($grid));

$fillers = [
    ["y" => $minY, "x" => 500]
];

$filled = [];
while ($fillers) {
    $newFillers = [];
    foreach ($fillers as $key => &$filler) {
        $x = &$filler["x"];
        $y = &$filler["y"];

        //fall until we reach something
        while (!isset($grid[$y][$x])) {
            $grid[$y][$x] = "|";
            $filled[$y."_".$x] = true;
            $y++;
            if ($y > $maxY) { // We reched the bottom
                continue 2;
            }
        }

        if ($grid[$y][$x] == "|") {
            // we landed in water! gtfo
            continue;
        }

        //fill to the sides
        $xSpread = 0;
        $rightStopReason = $leftStopReason = false;
        while (true) {
            if ($leftStopReason == "WALL" && $rightStopReason == "WALL") {
                //wall on both sides; step up, start at same position
                $y--;
                $xSpread = 0;
                $leftStopReason = $rightStopReason = false;
            } else if (
                ($leftStopReason == "WALL" && $rightStopReason == "FREEFALL") ||
                ($leftStopReason == "FREEFALL" && $rightStopReason == "WALL") ||
                ($leftStopReason == "FREEFALL" && $rightStopReason == "FREEFALL")
            ) {
                // We're done in this bucket. Fill the top layer with pipes, for some reason
                $origX = $x;
                while (isset($grid[$y][$x]) && $grid[$y][$x] == "~") {
                    $grid[$y][$x] = "|";
                    $x--;
                }
                $x = $origX + 1;
                while (isset($grid[$y][$x]) && $grid[$y][$x] == "~") {
                    $grid[$y][$x] = "|";
                    $x++;
                }
                break;
            }

            if (!$leftStopReason) { // We haven't stopped travelling to the left yet
                $leftX = $x-$xSpread;
                if (isset($grid[$y][$leftX]) && $grid[$y][$leftX] == "#") { // we hit a wall
                    $leftStopReason = "WALL";
                } else if (@$grid[$y+1][$leftX] == "|") { //we hit a stream! Annoying bug
                    #printGrid($grid, $fillers, $filled, 5000);
                    #var_dump($y, $x);
                    #die('hoi');
                    $leftStopReason = "FREEFALL";
                } else {
                    $grid[$y][$leftX] = "~";
                    $filled[$y."_".($leftX)] = true;
                    if (!isset($grid[$y+1][$leftX])) { // we hit an edge! create new filler?
                        $newFillers[] = ["x" => $leftX, "y" => $y+1];
                        $leftStopReason = "FREEFALL";
                    }
                }
            }

            if (!$rightStopReason) {  // We haven't stopped travelling to the right yet
                $rightX = $x+$xSpread;
                if (isset($grid[$y][$rightX]) && $grid[$y][$rightX] == "#") { // we hit a wall
                    $rightStopReason = "WALL";
                } else if (@$grid[$y+1][$rightX] == "|") { //we hit a stream! Annoying bug
                    #printGrid($grid, $fillers, $filled, 5000);
                    #var_dump($y, $x);
                    #die('hoi2');
                    $leftStopReason = "FREEFALL";
                } else {
                    $grid[$y][$rightX] = "~";
                    $filled[$y."_".($rightX)] = true;
                    if (!isset($grid[$y+1][$rightX])) { // we hit an edge! create new filler?
                        $newFillers[] = ["x" => $rightX, "y" => $y+1];
                        $rightStopReason = "FREEFALL";
                    }
                }
            }
            $xSpread++;
        }
    }

    // replace all the old fillers with the new ones
    $nf = [];
    foreach ($newFillers as $f) {
        $nf[$f["y"]."_".$f["x"]] = $f;
    }
    $fillers = $nf;
}
#printGrid($grid, $fillers, $filled, 1000000, $minY, $maxY);

echo "Part 1: ". count($filled) . "\n";
echo "Part 2: ". array_sum(array_map(function($row) { return count(array_filter($row, function($r) { return $r == "~"; })); }, $grid)) . "\n";

function printGrid($grid, $fillers, $filled, $sleep = 0, $from, $to) {
    #system("clear");
    $maxX = -INF;
    $minX = INF;
    foreach ($grid as $g) {
        $maxX = max($maxX, max(array_keys($g)));
        $minX = min($minX, min(array_keys($g)));
    }

    for ($y = $from; $y <= $to; $y++) {
        echo str_pad($y, 5, " ", STR_PAD_RIGHT);
        for ($x = $minX; $x <= $maxX; $x++) {
            if (isset($filled[$y."_".$x])) {
                echo "\033[1;32m";
            }
            if (isset($fillers[$y . "_" . $x])) {
                echo "▼";
            } else {
                echo isset($grid[$y][$x]) ? $grid[$y][$x] : ".";
            }
            if (isset($filled[$y."_".$x])) {
                echo "\033[0m";
            }
        }
        echo "\n";
    }
    echo "\n";
    if ($sleep) {
        usleep($sleep);
    }
}
