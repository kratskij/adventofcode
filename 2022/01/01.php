<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);

$input = $ir->lines();

$i = 0;
$elves = [];
foreach ($input as $weight) {
    if ($weight === "") {
        $i++;
        continue;
    }
    $elves[$i] = ($elves[$i] ?? 0) + (int)$weight;
}

rsort($elves);

$p1 = current($elves);
$p2 = array_sum(array_slice($elves, 0, 3));

echo "P1: $p1\nP2: $p2\n";
