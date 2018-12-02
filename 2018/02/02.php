<?php

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once("../inputReader.php");
$ir = new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file);
$input = $ir->lines();

$sum = [];
$lev = ["lowest" => INF];
foreach ($input as $line) {
    $charCount = [];
    foreach (str_split($line) as $c) {
        $charCount[$c] = (isset($charCount[$c])) ? $charCount[$c] + 1 : 1;
    }
    $charCount = array_flip($charCount);
    foreach ($charCount as $count => $null) {
        $sum[$count] = (isset($sum[$count])) ? $sum[$count] + 1 : 1;
    }

    foreach ($input as $line2) {
        if ($line2 == $line) { //to avoid comparing both ways
            break;
        }

        $diff = 0;
        $letters = "";
        foreach (str_split($line2) as $i => $c) {
            if ($c != $line[$i]) {
                $diff++;
            } else {
                $letters .= $c;
            }
        }

        if ($diff < $lev["lowest"]) {
            $lev["lowest"] = $diff;
            $lev["letters"] = $letters;
        }
    }
}
echo "Part 1: " . $sum[2] * $sum[3] . "\n";
echo "Part 2: " . $lev["letters"] . "\n";
#fonbwmjzquwtapeyzikghtvdxl
