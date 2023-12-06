<?php

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = $ir->extractNumbers();

$parts = [
    "p1" => array_combine($input[0], $input[1]),
    "p2" => [
        implode($input[0]) => implode($input[1]),
    ]
];

$p1 = $p2 = 1;
foreach ($parts as $part => $times) {
    foreach ($times as $time => $record) {
        $ways = 0;
        for ($i = 0; $i < $time; $i++) {
            $distance = $i * ($time - $i);
            if ($distance > $record) {
                $ways++;
            }
        }
        $$part *= $ways;
    }
}

echo "P1: $p1\nP2: $p2\n";
