<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once(__DIR__."/../inputReader.php");

require_once(__DIR__ . '/Drone.php');

$ir = new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file);
$code = $ir->trim(true)->explode(",");

echo "Part 1: " . countBeamPixels($code, 50) . "\n";
echo "Part 2: " . findClosestOfSize($code, 100) . "\n";

function countBeamPixels($code, $size) {
    $c = 0;
    for ($y = 0; $y < $size; $y++) {
        for ($x = 0; $x < $size; $x++) {
            if ((new Drone($code))->goto($x, $y) == 1) {
                $c++;
            }
        }
    }
    return $c;
}

function findClosestOfSize($code, $size) {
    $x = $y = 0;

    $fx = false;
    while (true) {
        while ((new Drone($code))->goto($x, $y) == 0) {
            if ($x > $size * 10) {
                $x = 0;
                $y++;
                continue 2;
            }
            $x++;
        }
        if ((new Drone($code))->goto($x + $size - 1, $y - $size + 1) == 1) {
            return ($x * 10000 + $y - $size + 1);
        } else {
            $y++;
            $x -= 1;
        }
    }
}
