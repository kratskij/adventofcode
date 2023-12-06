<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = $ir->lines();

preg_match_all("/(\d+)/", $input[0], $times);
preg_match_all("/(\d+)/", $input[1], $records);

$map = [
    "p1" => array_combine($times[0], $records[0]),
    "p2" => [
        implode($times[0]) => implode($records[0]),
    ]
];

$p1 = $p2 = 1;
foreach ($map as $part => $times) {
    foreach ($times as $time => $record) {
        $ways = [];
        for ($i = 0; $i < $time; $i++) {
            $distance = $i * ($time - $i);
            if ($distance > $record) {
                $ways[$i] = $distance;
            }
        }
        $$part *= count($ways);
    }
}

echo "P1: $p1\nP2: $p2\n";
