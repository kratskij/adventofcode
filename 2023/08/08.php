<?php

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");
require_once(__DIR__."/../../Toolbox.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = $ir->lines();

$dirs = str_split(array_shift($input));
array_shift($input);

$maps = [];
foreach ($input as $k => $line) {
    $line = str_replace(["(", ")", "= ", ","], "", $line);
    $parts = explode(" ", $line);
    [$src, $l, $r] = $parts;

    $maps[$src] = [
        "L" => $l,
        "R" => $r
    ];
}

$len = count($dirs);

$p1 = 0;
$current = "AAA";
while ($current != "ZZZ") {
    $current = $maps[$current][$dirs[$p1 % $len]];
    $p1++;
}

$currents = array_filter(array_keys($maps), function($map) { return ($map[2] == "A"); });
$firstHits = [];
foreach ($currents as $c) {
    $i = 0;
    while ($c[2] != "Z") {
        $c = $maps[$c][$dirs[$i % $len]];
        $i++;
    }
    $firstHits[] = $i;
}
$p2 = lcm($firstHits);

echo "P1: $p1\nP2: $p2\n";
