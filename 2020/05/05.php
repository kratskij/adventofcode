<?php

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim();
$input = $ir->lines();

$seats = [];

foreach ($input as $line) {
    $line = str_replace(["F", "B", "L", "R"], ["0", "1", "0", "1"], $line);
    $id = bindec($line);
    $seats[$id] = $id;
}

echo "P1: " . max($seats) . "\n";

foreach ($seats as $id) {
    if (!isset($seats[$id+1]) && isset($seats[$id+2])) {
        echo "P2: " . ($id+1) . "\n";
    }
}
