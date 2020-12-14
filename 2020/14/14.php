<?php

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = $ir->lines();

$memory = $memory2 = [];

foreach ($input as $k => $line) {
    $words = explode(" = ", $line);
    switch (explode("[", $words[0])[0]) {
        case "mask":
            $mask = 0;
            $combos = [0];
            $maskLength = strlen($words[1]);
            foreach (str_split($words[1]) as $i => $c) {
                $bit = pow(2, 35-$i);
                if ($c == "X") {
                    foreach ($combos as $combo) {
                        $combos[] = $combo | $bit;
                    }
                } else {
                    $mask = $mask | ((int)$c*$bit);
                }
            }
            $xMask = max($combos);
            break;
        case "mem":
            $pos = (int)substr($words[0], 4, -1);
            $val = (int)$words[1];
            foreach ($combos as $combo) {
                $addr = (($pos & ~$xMask) | $mask) | $combo;
                $memory2[$addr] = $val;
            }

            $memory[$pos] = ($val & $xMask) | $mask;
            break;
    }
}

echo sprintf(
    "P1: %s\nP2: %s\n",
    array_sum($memory),
    array_sum($memory2)
);
