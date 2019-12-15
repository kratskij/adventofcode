<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once(__DIR__."/../inputReader.php");
require_once(__DIR__ . '/RepairDroid.php');

$ir = new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file);
$code = $ir->trim(true)->explode(",");

$animate = false;

$repairDroid = new RepairDroid($code);

$repairDroid->mapArea($animate);

$distanceToOxygen = $repairDroid->shortestPath($repairDroid->getMap()->getOxygenPosition());
echo "Part 1: $distanceToOxygen\n";

$oxygenPosition = $repairDroid->getMap()->getOxygenPosition();
$repairDroid->setY($oxygenPosition[0]);
$repairDroid->setX($oxygenPosition[1]);
$oxygenSpreadTime = $repairDroid->spreadTime($repairDroid->getMap()->getOxygenPosition());
echo "Part 2: $oxygenSpreadTime\n";
