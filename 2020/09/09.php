<?php

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = $ir->lines();
$input = array_map("intval", $input);

$size = ($test ? 5 : 25);

$l = count($input);
for ($line = $size; $line < $l; $line++) {
    $sums = [];
    for ($j = $line-$size; $j < $line; $j++) {
        for ($x = $line-$size; $x < $line; $x++) {
            $sums[$input[$x]+$input[$j]] = $input[$x] . "+" . $input[$j];
        }
    }
    if (!isset($sums[$input[$line]])) {
        $p1 = $input[$line];
        break;
    }
}

foreach ($input as $k => $i) {
    $x = $k;
    $sum = 0;
    $nums = [];
    while ($x > 0) {
        $sum += $input[$x];
        $nums[] = $input[$x];
        if ($sum == $p1) {
            $p2 = max($nums) + min($nums);
            break  2;
        } else if ($sum >= $p1) {
            break;
        }
        $x--;
    }
}

echo "P1: $p1\nP2: $p2\n";
