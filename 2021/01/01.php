<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = $ir->lines();

$p1 = $p2 = 0;
foreach ($input as $k => $line) {
    $p1 += (isset($input[$k-1]) && $line > $input[$k-1]) ? 1 : 0;
    $p2 += (isset($input[$k-3]) && array_sum(array_slice($input, $k-2, 3)) > array_sum(array_slice($input, $k-3, 3))) ? 1 : 0;
}

echo "P1: $p1\nP2: $p2\n";
