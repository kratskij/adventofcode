<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim();

$input = $ir->lines();

$ans = 0;

foreach ($input as $k => $line) {
    $sms = strlen($line) <= 160;
    $tweet = mb_strlen($line) <= 140;
    if ($sms && $tweet) {
        $ans += 13;
    } else if ($sms) {
        $ans += 11;
    } else if ($tweet) {
        $ans += 7;
    }
}
echo "Answer: $ans\n";
