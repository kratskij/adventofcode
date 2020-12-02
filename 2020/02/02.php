<?php

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = $ir->regex("(\d+)-(\d+)\s(\w)\:\s(\w+)");
$a1 = $a2 = 0;

foreach ($input as $k => $i) {
    list($min, $max, $char, $pw) = $i;

    $x = substr_count($i[3], $i[2]);
    if ($x >= $min && $x<= $max) {
        $a1++;
    }

    if (($pw[$min-1] == $char xor $pw[$max-1] == $char)) {
        $a2++;
    }
}
echo "Part 1: $a1\nPart 2: $a2\n";
