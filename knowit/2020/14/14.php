<?php

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");
require_once(__DIR__ . DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."Toolbox.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);

$numbers = [0 => 0, 1 => 1];
$idx = [0 => true, 1 => true];
for ($n = 2; $n <= 1800813; $n++) {
    $r = $numbers[$n-2] - $n;
    if ($r < 0 || isset($idx[$r])) {
        $r = $numbers[$n-2] + $n;
    }
    $numbers[$n] = $r;
    $idx[$r] = true;
}

echo count(array_filter($numbers, "isPrime")) . "\n";

"Please stop refreshing <google product>, we'll never get back up with all these requests"
