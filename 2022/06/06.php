<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);

$input = $ir->chars();

$solves = [
    4 => false,
    14 => false
];

$l = count($input);
foreach ($solves as $length => $null) {
    for ($i = $length; $i < $l; $i++) {
        $sub = array_slice($input, $i-$length, $length);
        if (count(array_unique($sub)) == count($sub)) {
            // all unique!
            $solves[$length] = $i;
            break;
        }
    }
}

list($p1, $p2) = array_values($solves);

echo "P1: $p1\nP2: $p2\n";
