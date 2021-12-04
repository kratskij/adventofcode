<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = $ir->grid(["0" => 0, "1" => 1]);

$p1 = $p2 = false;

$transposed = [];
for ($i = 0; $i < count($input[0]); $i++) {
    foreach ($input as $n => $line) {
        $transposed[$i][$n] = $line[$i];
    }
}

$gamma = $eps = [];
foreach ($transposed as $col => $values) {
    $gamma[$col] = (int)(array_sum($values) > (count($values) / 2));
    $eps[$col] = (int)!$gamma[$col];
}
$p1 = intify($gamma) * intify($eps);
$p2 = intify(reduce($input, false)) * intify(reduce($input, true));

echo "P1: $p1\nP2: $p2\n";


function intify(array $arr) {
    return bindec(implode("", $arr));
}

function reduce($input, $invert) {
    $i = 0;
    while (count($input) > 1) {
        $colValues = [];
        foreach ($input as $r => $v) {
            $colValues[] = $v[$i];
        }
        if (array_sum($colValues) >= (count($colValues) / 2)) {
            $keep = 1 - (int)$invert;
        } else {
            $keep = 0 + (int)$invert;
        }
        $input = array_values(array_filter($input, function($inp) use ($i, $keep) {
            return ($inp[$i] == $keep);
        }));
        $i++;
    }

    return $input[0];
}
