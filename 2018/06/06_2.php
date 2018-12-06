<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once(__DIR__."/../inputReader.php");

$ir = new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file);
$input = $ir->regex("(\d+)\,\s(\d+)");

$grid = [];

$maxX = -INF;
$maxY = -INF;
$minX = INF;
$minY = INF;

$nodes = [];
foreach ($input as $k => $i) {
    list($y, $x) = $i;
    $y = (int)$y;
    $x = (int)$x;

    $maxX = max($x, $maxX);
    $maxY = max($y, $maxY);
    $minX = min($x, $minX);
    $minY = min($y, $minY);

    $grid[$x][$y] = $k;
    $nodes[] = [$x, $y, $k];
}


$area = [];

$distances = [];
for ($x = $minX; $x <= $maxX; $x++) {
    for ($y = $minY; $y <= $maxY; $y++) {
        #echo "At $x,$y\n";
        $closestDist = INF;
        $closestId = false;
        $totalDistance = 0;
        foreach ($nodes as $n) {
            $distance = abs(abs($n[0])-abs($x)) + abs(abs($n[1])-abs($y));
            $totalDistance += $distance;
            #echo $distance."\n";
            if ($distance == $closestDist) {
                #echo "equal\n";
                $closestId = false;
            } else if ($distance < $closestDist) {
                #echo "closest!\n";
                $closestId = $n[2];
                $closestDist = $distance;
            }
        }
        if ($closestId !== false) {
            #echo "FOUND $closestId\n";
            $area[$x][$y] = $closestId;
        }
        $distances[$x][$y] = $totalDistance;
    }
}
/*
for ($x = $minX; $x <= $maxX; $x++) {
    for ($y = $minY; $y <= $maxY; $y++) {
        if (isset($grid[$x][$y])) {
            echo strtoupper(chr(65+$grid[$x][$y]));
        } else if (isset($area[$x][$y])) {
            echo strtolower(chr(65+$area[$x][$y]));
        } else {
            echo ".";
        }
    }
    echo "\n";
}*/
var_Dump($distances);
$counts = [];
$boundaries = [];
for ($x = $minX; $x <= $maxX; $x++) {
    for ($y = $minY; $y <= $maxY; $y++) {
        if (isset($area[$x][$y])) {
            $counts[$area[$x][$y]]++;
            if ($x == $minX || $y == $minY || $x == $maxX || $y == $maxY) {
                $boundaries[$area[$x][$y]] = true;
            }
        }
    }
}

$max = 0;
foreach ($counts as $i => $c) {
    if (!isset($boundaries[$i])) {
        $max = max($max, $c);
    }
}

$c=0;
foreach ($distances as $x => $row) {
    foreach ($row as $y => $distance) {
        if ($distance < ($test ? 32 : 10000)) {
            $c++;
        }
    }
}
echo "\n$c\n";die();#93240
$max = 0;
foreach ($counts as $i => $c) {
    if (!isset($boundaries[$i])) {
        $max = max($max, $c);
    }
}
echo $max."\n";
