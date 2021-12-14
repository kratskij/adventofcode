<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = $ir->lines();
$template = false;

$rules = [];
foreach ($input as $k => $line) {
    if (!$template) { $template = $line; continue; }
    if (empty($line)) { continue; }
    list($from, $to) = explode(" -> ", $line);
    $rules[$from] = $to;
}


$charCounts = $pairCounts = [];
foreach (str_split($template) as $k => $char) {
    increment($charCounts[$char], 1);
    if (isset($template[$k+1])) {
        $idx = $template[$k] . $template[$k+1];
        increment($pairCounts[$idx], 1);
    }
}

for ($i = 0; $i < 40; $i++) {
    $newPairCounts = [];
    foreach ($pairCounts as $idx => $c) {
        increment($charCounts[$rules[$idx]], $c);
        foreach ([$idx[0] . $rules[$idx], $rules[$idx] . $idx[1]] as $nIdx) {
            increment($newPairCounts[$nIdx], $c);
        }
    }
    $pairCounts = $newPairCounts;
    if ($i == 9) {
        $p1 = max($charCounts) - min($charCounts);
    }
}
$p2 = max($charCounts) - min($charCounts);

echo "P1: $p1\nP2: $p2\n";

function increment(&$var, $i) {
    $var = ($var ?? 0) + $i;
}
