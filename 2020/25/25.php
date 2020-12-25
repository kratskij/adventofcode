<?php

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");
$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
list($doorKey, $cardKey) = $ir->lines();

$doorLoops = 0;
$val = 1;
while ($val != $doorKey) {
    $val = ($val * 7) % 20201227;
    $doorLoops++;
}

$p1 = 1;
for ($i = 0; $i < $doorLoops; $i++) {
    $p1 = ($p1 * $cardKey) % 20201227;
}

echo "P1: $p1\n";
