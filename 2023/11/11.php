<?php

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);

$grid = $ir->grid(["#" => true, "." => false]);

$emptyRows = [];
$setCols = [];

foreach ($grid as $y => $row) {
    if (empty(array_filter($row))) {
        $emptyRows[$y] = $y;
    }
    foreach ($row as $x => $val) {
        if ($val) {
            $values[] = [$y, $x];
            $setCols[$x] = $x;
        }
    }
}
$emptyCols = array_diff(array_keys($grid[0]), $setCols);

$pairs = [];
foreach ($values as $val) {
    foreach ($values as $val2) {
        if ($val < $val2) {
            $pairs[] = [$val, $val2];
        }
    }
}

$p1 = $p2 = 0;

foreach ($pairs as $pair) {
    [$pair1, $pair2] = $pair;
    [$p1y, $p1x] = $pair1;
    [$p2y, $p2x] = $pair2;

    $distance = abs($p2y - $p1y) + abs($p2x - $p1x);
    $expansions = 0;
    foreach ($emptyCols as $e) {
        if (($p1x < $e && $e < $p2x) || ($p2x < $e && $e < $p1x)) {
            $expansions++;
        }
    }
    foreach ($emptyRows as $e) {
        if (($p1y < $e && $e < $p2y) || ($p2y < $e && $e < $p1y)) {
            $expansions++;
        }
    }
    $p1 += $distance + $expansions;
    $p2 += $distance + ($expansions * 999999);
}

echo "P1: $p1\nP2: $p2\n";
