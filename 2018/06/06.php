<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once(__DIR__."/../inputReader.php");

$ir = new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file);
$input = $ir->regex("(\d+)\,\s(\d+)");

$regionLimit = $test ? 32 : 10000;

$nodes = [];
foreach ($input as $id => $line) {
    $nodes[$id] = [
        "x" => (int)$line[1],
        "y" => (int)$line[0],
        "size" => 0,
        "infinite" => false
    ];
}
$minX = min(array_map(function($node) { return $node["x"]; }, $nodes));
$maxX = max(array_map(function($node) { return $node["x"]; }, $nodes));
$minY = min(array_map(function($node) { return $node["y"]; }, $nodes));
$maxY = max(array_map(function($node) { return $node["y"]; }, $nodes));

$totalDistancesRegionCount = 0;
for ($x = $minX; $x <= $maxX; $x++) {
    $edgeX = ($x == $minX) || ($x == $maxX);
    for ($y = $minY; $y <= $maxY; $y++) {
        $edgeY = ($y == $minY) || ($y == $maxY);
        $closest = ["distance" => INF, "id" => false];
        $totalDistance = 0;
        foreach ($nodes as $id => $node) {
            $distance = abs($node["x"]-$x) + abs($node["y"]-$y);
            $totalDistance += $distance;
            if ($distance < $closest["distance"]) {
                $closest["id"] = $id;
                $closest["distance"] = $distance;
            } elseif ($distance == $closest["distance"]) {
                $closest["id"] = false;
            }
        }
        if ($totalDistance < $regionLimit) {
            $totalDistancesRegionCount++;
        }
        if ($closest["id"] !== false) {
            $nodes[$closest["id"]]["size"]++;
            if ($edgeX || $edgeY) {
                $nodes[$closest["id"]]["infinite"] = true;
            }
        }
    }
}
$max = max(array_map(function($node) { return $node["infinite"] ? -INF : $node["size"]; }, $nodes));
echo "Part 1: $max\n";
echo "Part 2: $totalDistancesRegionCount\n";
