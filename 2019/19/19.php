<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once(__DIR__."/../inputReader.php");

require_once(__DIR__ . '/Drone.php');

$ir = new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file);
$code = $ir->trim(true)->explode(",");

$drone = new Drone($code);

echo "Part 1: " . $drone->countBeamPixels(50) . "\n";

$closestFit = $drone->findClosestOfSize(100);
echo "Part 2: " . ($closestFit[0] * 10000 + $closestFit[1]) . "\n";
