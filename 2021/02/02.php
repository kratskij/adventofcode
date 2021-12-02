<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = $ir->regex("(\w+)\s(\d+)");

$horizontal = $depth1 = $depth2 = $aim = 0;

foreach ($input as $k => $line) {
    switch ($line[0]) {
        case "forward":
            $horizontal += $line[1];
            $depth2 += ($aim * $line[1]);
            break;
        case "down":
            $depth1 += $line[1];
            $aim += $line[1];
            break;
        case "up":
            $depth1 -= $line[1];
            $aim -= $line[1];
            break;
    }
}

$p1 = $horizontal*$depth1;
$p2 = $horizontal*$depth2;

echo "P1: $p1\nP2: $p2\n";
