<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = $ir->explode(",");
$input = array_map("intval", $input);

$p1 = $p2 = PHP_INT_MAX;
for ($pos = 0; $pos <= max($input); $pos++) {
    $p1fuel = $p2fuel = 0;
    foreach ($input as $k => $v) {
        $d = abs($v - $pos);
        $p1fuel += $d;
        $p2fuel += $d * ($d + 1) / 2;
    }
    $p1 = min($p1fuel, $p1);
    $p2 = min($p2fuel, $p2);
}
echo "P1: $p1\nP2: $p2\n";
