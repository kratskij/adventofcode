<?php

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = $ir->explode("\n\n");
$val = false;

$anyCount = $allCount = 0;

foreach ($input as $group) {
    $people = explode("\n", $group);
    $u = $all = [];
    foreach ($people as $p) {
        $questionsYes = str_split($p);
        $u = array_merge($u, array_unique($questionsYes));

        foreach ($questionsYes as $q) {
            if (!isset($all[$q])) {
                $all[$q] = 0;
            }
            $all[$q]++;
        }
    }

    $l = count($people);
    foreach ($all as $x) {
        if ($x == $l) {
            $allCount++;
        }
    }

    $anyCount += count(array_unique($u));
}
echo sprintf("P1: %s\nP2: %s\n", $anyCount, $allCount);
