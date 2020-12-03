<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$grid = $ir->grid(["#" => true,"." => false,]);

$p1 = treesEncountered($grid, 3, 1);
$slopes = [
    [1, 1],
    [3, 1],
    [5, 1],
    [7, 1],
    [1, 2],
];
$p2 = 1;
foreach ($slopes as $slope) {
    $p2 *= treesEncountered($grid, $slope[0], $slope[1]);
}

echo sprintf("P1: %d\nP2: %d\n", $p1, $p2);

function treesEncountered($grid, $xd, $yd) {
    $l = count($grid[0]);
    $s = 0; $y = 0; $x = 0;
    while (isset($grid[$y])) {
        if ($grid[$y][$x%$l]) {
            $s++;
        }
        $x += $xd;
        $y += $yd;
    }
    return $s;
}
