<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");
#require_once __DIR__."/../Util.php";

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim();

$input = $ir->lines();

$ans = 0;

$c = [];
foreach ($input as $k => $line) {
    $uts = (new \DateTime($line))->format("U");
    if (!isset($c[$uts])) {
        $c[$uts] = 0;
    }
    if (++$c[$uts] >= 4) {
        $ans = date(\DateTime::ATOM, $uts);
        break;
    }
}
echo "Answer: $ans\n";
