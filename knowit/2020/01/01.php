<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim();
$input = array_flip($ir->explode(","));
for ($i = 0; $i < 100000; $i++) {
    if (!isset($input[$i])) {
        echo $i."\n";
    }
}
