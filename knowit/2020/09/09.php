<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$grid = $ir->grid(["F" => 0, "S" => 1]);

$days = 0;
while (true) {
    $days++;
    $copy = $grid;
    $change = false;
    foreach ($grid as $y => $row) {
        foreach ($row as $x => $sick) {
            if (
                $sick === 0 &&
                (
                    ($grid[$y-1][$x] ?? 0) +
                    ($grid[$y+1][$x] ?? 0) +
                    ($grid[$y][$x-1] ?? 0) +
                    ($grid[$y][$x+1] ?? 0)
                ) >= 2
            ) {
                $change = true;
                $copy[$y][$x] = true;
            }
        }
    }
    if (!$change) {
        break;
    }
    $grid = $copy;
}

echo "Days elapsed: $days\n";
