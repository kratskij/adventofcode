<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");
#require_once __DIR__."/../Util.php";

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);

$input = $ir->lines();


$words = [ "one", "two", "three", "four", "five", "six", "seven", "eight", "nine" ];

$p1 = $p2 = 0;
foreach ($input as $k => $line) {
    $p1first = $p1last = $p2first = $p2last = false;
    
    for ($i = 0; $i < strlen($line); $i++) {
        $p1num = $p2num = false;
        if (is_numeric($line[$i])) {
            $p1num = $line[$i];
            $p2num = $line[$i];
        } else {
            foreach ($words as $key => $word) {
                if (substr($line, $i, strlen($word)) == $word) {
                    $p2num = $key + 1;
                    break;
                }
            }
        }
        if ($p1num) {
            $p1first = $p1first ?: $p1num;
            $p1last = $p1num;
        }
        if ($p2num) {
            $p2first = $p2first ?: $p2num;
            $p2last = $p2num;
        }
    }
    $p1 += (int)($p1first.$p1last);
    $p2 += (int)($p2first.$p2last);
}

echo "P1: $p1\nP2: $p2\n";
