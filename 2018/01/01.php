<?php

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once("../inputReader.php");
$frequencies = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->lines();

$currentFrequency = 0;
$visited = [];
$statuses = [1 => false, 2 => false];

while (count(array_filter($statuses)) < 2) {
    foreach ($frequencies as $frequency) {
        $currentFrequency += $frequency;
        if (isset($visited[$currentFrequency])) {
            $statuses[2] = true;
            echo "Part 2: $currentFrequency\n";
            break;
        }
        $visited[$currentFrequency] = true;
    }
    if (!$statuses[1]) {
        $statuses[1] = true;
        echo "Part 1: $currentFrequency\n";
    }
}
