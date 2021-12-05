<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);

$input = $ir->regex("(\d+)\,(\d+)\s-\>\s(\d+)\,(\d+)");
$grid = [];
foreach ($input as $k => $line) {
    list($x, $y, $x2, $y2) = $line;

    $isDiag = max($x, $x2) - min($x, $x2) == max($y, $y2) - min($y, $y2);
    if ($isDiag || $x == $x2 || $y == $y2) {
        $xd = ($x2 > $x) ? 1 : ($x2 < $x ? -1 : 0);
        $yd = ($y2 > $y) ? 1 : ($y2 < $y ? -1 : 0);
        registerPoint($grid, $x, $y, $isDiag);
        while ($x != $x2 || $y != $y2) {
            $x += $xd;
            $y += $yd;
            registerPoint($grid, $x, $y, $isDiag);
        }
    }
}

$p1 = $p2 = 0;
foreach ($grid as $y => $row) {
    foreach ($row as $x => $info) {
        if ($info["p1"] > 1) { $p1++; }
        if ($info["p2"] > 1) { $p2++; }
    }
}
echo "P1: $p1\nP2: $p2\n";

function registerPoint(&$grid, $x, $y, $isDiag) {
    if (!isset($grid[$y][$x])) {
        $grid[$y][$x] = ["p1" => ($isDiag ? 0 : 1), "p2" => 1];
    } else {
        $grid[$y][$x]["p2"]++;
        if (!$isDiag) {
            $grid[$y][$x]["p1"]++;
        }
    }
}
