<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = array_map("intval", $ir->lines());

$answers = [];
foreach ($input as $i => $x) {
    foreach ($input as $j => $y) {
        if ( $x + $y == 2020) {
            $answers[1] = ($x * $y);
        }
        foreach ($input as $k => $z) {
            if ( $x + $y + $z == 2020) {
                $answers[2] = ($x * $y * $z);
            }
        }
    }
}

echo "Part 1: {$answers[1]}\nPart 2: {$answers[2]}\n";
