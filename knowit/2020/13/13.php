<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$chars = $ir->chars();

$pos = [];
for ($i = 1 ; $i<= 26; $i++) {
    $pos[chr($i+96)] = $i;
}
$arr = [];
$ans = "";
foreach ($chars as $c) {
    if (!isset($arr[$c])) {
        $arr[$c] = 0;
    }
    $arr[$c]++;
    if ($arr[$c] == $pos[$c]) {
        $ans .= $c;
    }
}
echo "$ans\n";
