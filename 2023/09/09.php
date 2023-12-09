<?php

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$lines = $ir->extractNumbers(true);

$p1 = $p2 = 0;
foreach ($lines as $y => $row) {
    $p1 += solve($row);
    $p2 += solve(array_reverse($row));
}

echo "P1: $p1\nP2: $p2\n";

function solve($numbers) {
    $diffSrc = $numbers;
    $history = [$numbers];
    while (!empty(array_filter($diffSrc))) {
        $diff = [];
        foreach ($diffSrc as $k => $n) {
            if (isset($diffSrc[$k+1])) {
                $diff[$k+1] = $diffSrc[$k+1] - $n;
            }
        }
        $history[] = array_values($diff);
        $diffSrc = $diff;
    }
    $history[count($history) - 1][] = 0;
    $prev = array_pop($history);
    while ($curr = array_pop($history)) {
        $l = count($curr);
        $curr[] = $curr[$l - 1] + $prev[$l - 1];
        $prev = $curr;
    }

    return end($prev);
}
