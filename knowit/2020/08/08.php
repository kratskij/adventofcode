<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = $ir->lines();

$locations = [];
$x = $y = 0;
foreach ($input as $i => $line) {
    $parts = explode(" (", $line);
    if (count($parts) == 2) {
        $parts[1] = explode(")", $parts[1])[0];
        list($x, $y) = explode(", ", $parts[1]);

        $location = explode(":", $parts[0])[0];
        $locations[$location] = [$y, $x, 0];
    } else {
        $toX = $locations[$line][1];
        $toY = $locations[$line][0];
        $distLeft = abs($x - $toX) + abs($y - $toY);
        $considerLocations = [];
        foreach ($locations as $location => $data) {
            if (abs($data[1] - $toX) + abs($data[0] - $toY) > $distLeft + 50) {
                $locations[$location][2] += $distLeft;
            } else {
                $considerLocations[] = $location;
            }
        };
        while (true) {
            if ($x != $toX) {
                $x += ($toX > $x) ? 1 : -1;
            } else if ($y != $toY) {
                $y += ($toY > $y) ? 1 : -1;
            }
            foreach ($considerLocations as $location) {
                $dist = abs($locations[$location][0] - $y) + abs($locations[$location][1] - $x);
                if ($dist == 0) {

                } else if ($dist < 5) {
                    $locations[$location][2] += 0.25;
                } else if ($dist < 20) {
                    $locations[$location][2] += 0.5;
                } else if ($dist < 50) {
                    $locations[$location][2] += 0.75;
                } else {
                    $locations[$location][2] += 1;
                }
            }
            if ($x == $toX && $y == $toY) {
                break;
            }
        }
    }
}

$max = 0;
$min = PHP_INT_MAX;
foreach ($locations as $location => $data) {
    $min = min($min, $data[2]);
    $max = max($max, $data[2]);
}
#var_dump($locations);
echo ($max - $min) . "\n";

#24650.5:  WRONG
#25692.75: WRONG
#6845.25:  CORRECT
