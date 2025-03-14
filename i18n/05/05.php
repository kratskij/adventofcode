<?php

require_once(__DIR__."/../inputReader.php");

$file = $argv[1] ?? "input";
$test = $file == "test";
$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim();
$grid = $ir->grid();

$w = count($grid[0]);
$h = count($grid);
$ans = $x = 0;

for ($y = 0; $y < $h; $y++) {
    if ($grid[$y][$x] === "💩") {
        $ans++;
    }
    $x = ($x + 2) % $w;
}

echo "Answer: $ans\n";
