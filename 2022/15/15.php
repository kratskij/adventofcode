<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = $ir->regex("Sensor at x=([\-\d]+), y=([\-\d]+): closest beacon is at x=([\-\d]+), y=([\-\d]+)");

if ($test) {
    $goalY = 10;
    $min = 0;
    $max = 20;
} else {
    $goalY = 2000000;
    $min = 0;
    $max = 4000000;
}

$p1 = $p2 = false;

$taken = [];
$beacons = $sensors = [];
foreach ($input as $k => $line) {
    list($sx, $sy, $bx, $by) = array_map("intval", $line);
    $beacons[$by][$bx] = [$sy,$sx];
    $distance = abs($sx-$bx)+abs($sy-$by);
    $sensors[] = [$sy,$sx,$distance];
    $startRow = $sy-$distance;
    $endRow = $sy+$distance;
    if ($startRow < $goalY && $endRow > $goalY) {
        $xDist = $distance - abs($sy-$goalY);
        $fromX = $sx-$xDist;
        $toX = $sx+$xDist;
        for ($i = $fromX; $i < $toX; $i++) {
            $taken[$i] = true;
        }
    }
}
$p1 = count($taken);

for ($y = $min; $y < $max; $y++) {
    $covers = $lookup = [];
    foreach ($sensors as $data) {
        list($sy, $sx, $distance) = $data;
        $startRow = $sy-$distance;
        $endRow = $sy+$distance;
        if ($startRow <= $y && $endRow >= $y) {
            $xDist = $distance - abs($sy-$y);
            $fromX = $sx-$xDist;
            $toX = $sx+$xDist;
            $covers[$fromX][$toX] = $toX;
            $lookup[$toX][$fromX] = $fromX;
        }
    }
    foreach ($covers as $fromX => $toXs) {
        foreach ($toXs as $toX) {
            if (
                !isset($lookup[$toX+1]) &&
                !isset($covers[$toX+1]) &&
                isset($covers[$toX+2]) &&
                !isset($beacons[$y][$toX+1]) &&
                !isset($sensors[$y][$toX+1])
            ) {
                $covered = false;
                foreach ($covers as $fromX2 => $toX2s) {
                    foreach ($toX2s as $toX2) {
                        if ($fromX2 <= $toX+1 && $toX2 >= $toX+1 ) {
                            $covered = true;
                            break 2;
                        }
                    }
                }
                if (!$covered) {
                    $p2 = ($toX + 1) * 4000000 + $y;
                    break 3;
                }
            }
        }
    }
}

echo "P1: $p1\nP2: $p2\n";

#7428931184833; too low
