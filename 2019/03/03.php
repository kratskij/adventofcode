<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once(__DIR__."/../inputReader.php");

$ir = new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file);
$input = $ir->trim(true)->lines();

$grid = [];
foreach ($input as $id => $wire) {
    $positions = explode(",", $wire);
    $x = $y = $count = 0;
    foreach ($positions as $move) {
        $dir = substr($move, 0, 1);
        $length = substr($move, 1);

        for ($i = 0; $i < $length; $i++) {
            switch ($dir) {
                case "U":
                    $y--;
                    break;
                case "R":
                    $x++;
                    break;
                case "L":
                    $x--;
                    break;
                case "D":
                    $y++;
                    break;

            }
            @$grid[$y][$x][$id][] = ++$count;
        }
    }
}

$minDistance = $minWireLength = PHP_INT_MAX;
foreach ($grid as $y => $row) {
    foreach ($row as $x => $wires) {
        if (count($wires) == 2) {
            $minDistance = min($minDistance, abs($y)+abs($x));
            $minWireLength = min($minWireLength, min($wires[0]) + min($wires[1]));
        }
    }
}

echo "Part 1: $minDistance\n";
echo "Part 2: $minWireLength\n";
