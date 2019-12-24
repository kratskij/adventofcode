<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once(__DIR__."/../inputReader.php");

require_once(__DIR__ . '/SpringDroid.php');

$ir = new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file);
$code = $ir->trim(true)->explode(",");

$droid = new SpringDroid($code);
echo "Part 1: " . $droid
    // if a or c is missing ...
    ->not("A", "J")
    ->not("C", "T")
    ->or( "T", "J")

    // and we have ground at d (to avoid jumping too early)
    ->and("D", "J")
    ->walk()
. "\n";

$droid->reset();

echo "Part 2: " . $droid
    // if a, b, c or d is missing ...
    ->not("A", "T")
    ->not("B", "J")
    ->or( "T", "J")
    ->not("C", "T")
    ->or( "T", "J")
    ->and("D", "J")

    ->not("J", "T") // store the inverse of j in t, to ensure that ...

    // ... e or h is ground
    ->or( "E", "T")
    ->or( "H", "T")
    ->and("T", "J")
    ->run()
. "\n";
