<?php

require_once(__DIR__."/../inputReader.php");
require_once(__DIR__."/../Util.php");

$file = $argv[1] ?? "input";
$test = $file == "test";

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = $ir->lines();
$ans = 0;

foreach ($input as $k => $line) {
    $nums = [];
    foreach (explode(" × ", $line) as $jNumLen) {
        $nums[] = Util::convertJpNumeral(mb_substr($jNumLen, 0, -1));
        $nums[] = Util::convertJpLength(mb_substr($jNumLen, -1));
    }
    $ans += array_product($nums);
}

echo "Answer: $ans\n";



