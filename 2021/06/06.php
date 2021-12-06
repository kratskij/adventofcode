<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = $ir->explode(",");
$input = array_map("intval", $input);

$p1 = $p2 = false;

$c = [];
foreach ($input as $i) {
    $c[$i] = ($c[$i] ?? 0) + 1;
}

$stop = 256;
for ($day = 1; $day <= $stop; $day++) {
    $new = [];
    foreach ($c as $k => $v) {
        if ($k == 0) {
            $new[6] = ($new[6] ?? 0) + $v;
            $new[8] = ($new[8] ?? 0) + $v;
        } else {
            $new[$k-1] = ($new[$k-1] ?? 0) + $v;
        }
    }
    $c = $new;
    if ($day == 80) {
        $p1 = array_sum($c);
    }
}
$p2 = array_sum($c);

echo "P1: $p1\nP2: $p2\n";
