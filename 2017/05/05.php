<?php

$test = false;

$file = ($test) ? "test" : "input";
$input = explode("\n", trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $file)));
$input = array_map("intval", $input);

$c = $i = 0;
$instr = $input;
while (true) {
    $line = $instr[$i];
    $c++;

    if (!isset($instr[$i + $line])) {
        break;
    }

    $instr[$i] = $instr[$i] + 1;

    $i += $line;
}
echo "Part 1: $c\n";

$c = $i = 0;
$instr = $input;
while (true) {
    $line = $instr[$i];
    $c++;

    if (!isset($instr[$i + $line])) {
        break;
    }

    $instr[$i] = ($line >= 3) ? $instr[$i] - 1 : $instr[$i] + 1;

    $i += $line;
}
echo "Part 2: $c\n";
