<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";

$a = ($test) ? 65 : 783;
$b = ($test) ? 8921 : 325;

$sum = 0;

for ($i = 0; $i < 40000000; $i++) {
    $a = ($a * 16807) % 2147483647;
    $b = ($b * 48271) % 2147483647;
    if (($a & 0xFFFF) == ($b & 0xFFFF)) {
        $sum++;
    }
}
echo "Part 1: $sum\n";

$a = ($test) ? 65 : 783;
$b = ($test) ? 8921 : 325;
$sum = 0;

for ($i = 0; $i < 5000000; $i++) {
    do $a = ($a * 16807) % 2147483647; while ($a & 0x3);
    do $b = ($b * 48271) % 2147483647; while ($b & 0x7);
    if (($a & 0xFFFF) == ($b & 0xFFFF)) {
        $sum++;
    }
}

echo "Part 2: $sum\n";
