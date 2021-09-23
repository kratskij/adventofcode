<?php

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = $ir->raw();

$rem = 10;
$c = 0;
foreach (str_split($input) as $k => $line) {
    if ($line == "1") {
        $rem += 2;
    }
    $rem--;
    $c++;
    if ($rem == 0) {
        break;
    }
}

echo $c;
