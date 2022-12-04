<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);

$input = $ir->lines();
$p1 = $p2 = 0;

foreach ($input as $k => $line) {
    $line = trim($line);
    list($one ,$two) = explode(",", $line);

    list($fromOne, $toOne) = array_map("intval", explode("-", $one));
    list($fromTwo, $toTwo) = array_map("intval", explode("-", $two));

    $p1 += ($fromOne <= $fromTwo && $toOne >= $toTwo) || ($fromTwo <= $fromOne && $toTwo >= $toOne);
    $p2 += ($fromOne <= $toTwo && $toOne >= $fromTwo) || ($fromTwo <= $toOne && $toTwo >= $fromOne);
}
echo "P1: $p1\nP2: $p2\n";
