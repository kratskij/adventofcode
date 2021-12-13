<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");
require_once(__DIR__."/../Util.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = $ir->lines();

$mode = true;
$grid = $folds = [];

foreach ($input as $k => $line) {
    if (empty($line)) {
        continue;
    } else if (strpos($line, "fold along") === false) {
        list($x, $y) = array_map("intval", explode(",", $line));
        $grid[$y][$x] = true;
    } else {
        list($dir, $offset) = explode("=", explode("fold along ", $line)[1]);
        $dy = ($dir == "y") ? (int)$offset : 0;
        $dx = ($dir == "x") ? (int)$offset : 0;

        $newGrid = [];
        foreach ($grid as $y => $row) {
            foreach ($row as $x => $val) {
                $newGrid[fold($y, $dy)][fold($x, $dx)] = true;
            }
        }
        $grid = $newGrid;

        $p1 = $p1 ?? array_sum(array_map("count", $grid));
    }
}

function fold($v, $dv) {
    return ($dv && $v > $dv) ? abs(2*$dv - $v) : $v;
}

$p2 = "\n" . Util::printGrid($grid);

echo "P1: $p1\nP2: $p2\n";
