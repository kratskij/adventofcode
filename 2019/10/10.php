<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once(__DIR__."/../inputReader.php");

$ir = new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file);
$input = $ir->lines();

// set up position of all asteroids, and store all asteroids in a one-dimensional array for easier access later
$grid = $positions = [];
foreach ($input as $y => $row) {
    foreach (str_split($row) as $x => $val) {
        if ($val == "#") {
            $grid[$y][$x] = [];
            $positions[] = ["y" => $y, "x" => $x];
        }
    }
}

// for each asteroid, store angle to all other asteroids (angle is key, distance is subkey, coords is value).
foreach ($grid as $y => $row) {
    foreach ($row as $x => $asteroid) {
        foreach ($positions as $p) {
            $yDist = $p["y"]-$y;
            $xDist = $p["x"]-$x;
            if ($yDist == 0 && $xDist == 0) {
                continue; // don't store self
            }
            $angle = rad2deg(atan2($yDist, $xDist));
            $angle += 90; // setting 0 = north
            if ($angle < 0) {
                $angle += 360; // ensuring we only have positive values. not really relevant, and easier to sort
            }
            $angle = (string)$angle; // cannot use float as index; convert to string
            if (!isset($grid[$y][$x][$angle])) {
                $grid[$y][$x][$angle] = [];
            }
            $distance = abs($yDist) + abs($xDist);
            $grid[$y][$x][$angle][$distance] = ["y" => $p["y"], "x" => $p["x"]];
        }
    }
}

// find base (asteroid which can see most other asteroids)
$base = [];
foreach ($grid as $y => $row) {
    foreach ($row as $x => $asteroid) {
        if (count($asteroid) > count($base)) {
            $base = $asteroid;
        }
    }
}
echo "Part 1: " . count($base) . "\n";

ksort($base); // sort all neighbours by angle
foreach ($base as $angle => $asteroids) {
    ksort($base[$angle]); // sort all neighbours in same angle by distance
}

$c = 0;
while ($base) {
    foreach ($base as $angle => $asteroids) {
        if (!empty($asteroids)) {
            $c++;
            $target = array_shift($asteroids);
            if ($c == 200) {
                $magicNumber = ($target["x"] * 100 + $target["y"]);
                echo "Part 2: $magicNumber\n";
                break 2;
            }
        }
    }
}
