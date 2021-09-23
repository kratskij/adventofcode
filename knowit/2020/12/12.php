<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$chars = str_split($ir->raw());
$level = 0;
$tree = [];
$name = "";
foreach ($chars as $c) {
    $ld = 0;
    switch ($c) {
        case " ":
            break;
        case "(":
            $ld+=1;
            break;
        case ")":
            $ld-=1;
            break;
        default:
            $name .= $c;
            continue 2;
    }
    if ($name) {
        if (!isset($tree[$level])) {
            $tree[$level] = 0;
        }
        $tree[$level]++;
        $name = "";
    }
    $level += $ld;
}
echo max($tree) . "\n";

#5965
