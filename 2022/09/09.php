<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");
require_once(__DIR__."/../Util.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = $ir->csv(" ");

$dirs = [
    "R" => [0,1],
    "D" => [1,0],
    "L" => [0,-1],
    "U" => [-1,0],
];

foreach ([2,10] as $knotCount) {
    $grid = [];
    $knots = [];
    for ($i = 0; $i < $knotCount; $i++) {
        $knots[] = [0,0];
    }

    foreach ($input as $k => $line) {
        list($dir, $val) = $line;
        list($dy, $dx) = $dirs[$dir];
        for ($i = 1; $i <= $val; $i++) {
            $knots[0][0] += $dy;
            $knots[0][1] += $dx;
            foreach ($knots as $id => $knot) {
                if ($id == 0) {
                    $prev = $knot;
                    continue;
                }
                $dist = abs($prev[0]-$knot[0])+abs($prev[1]-$knot[1]);
                if ($dist > 1) {
                    if ($prev[1] == $knot[1]) {
                        $knots[$id][0] += ($prev[0]-$knot[0])/2;
                    } else if ($prev[0] == $knot[0]) {
                        $knots[$id][1] += ($prev[1]-$knot[1])/2;
                    } else if ($dist > 2) {
                        $knots[$id][0] += ($prev[0]-$knot[0])/abs($prev[0]-$knot[0]);
                        $knots[$id][1] += ($prev[1]-$knot[1])/abs($prev[1]-$knot[1]);
                    }
                    $knot = $knots[$id];
                }
                $prev = $knot;
            }
            $grid[$knots[$knotCount-1][0]][$knots[$knotCount-1][1]] = true;
        }
        $dbgGrid = [];
        foreach ($knots as $id => $knot) {
            $dbgGrid[$knot[0]][$knot[1]] = $id;
        }
    }
    $tailCounts[] = array_sum(array_map("count", $grid));
}

list($p1, $p2) = $tailCounts;
echo "P1: $p1\nP2: $p2\n";
