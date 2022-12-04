<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = $ir->lines();

$p1 = $p2 = 0;
foreach ($input as $k => $line) {
    $splitPos = strlen($line) / 2;
    $one = str_split(substr($line, 0, $splitPos));
    $two = str_split(substr($line, $splitPos));

    $same = array_unique(array_intersect($one, $two));
    foreach ($same as $c) {
        if (ord($c) >= 95) {
            $p1 += ord($c) - 96;
        } else {
            $p1 += ord($c) - 64 + 26;
        }
    }

    if (($k % 3) == 2) {
        $one = str_split($input[$k]);
        $two = str_split($input[$k-1]);
        $three = str_split($input[$k-2]);

        $badge = trim(implode("", array_unique(array_intersect($one, $two, $three))));
        if (ord($badge) >= 95) {
            $p2 += ord($badge) - 96;
        } else {
            $p2 += ord($badge) - 64 + 26;
        }
    }
}

echo "P1: $p1\nP2: $p2\n";
