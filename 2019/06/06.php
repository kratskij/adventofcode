<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once(__DIR__."/../inputReader.php");

$ir = new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file);
$input = $ir->trim()->lines();

$orbits = [];

foreach ($input as $k => $i) {
    list($object, $satellite) = explode(")", $i);
    $orbits[$object][$satellite] = $satellite;
    $orbits[$satellite][$object] = $object;
}

echo "Part 1: " . array_sum(distances($orbits, "COM")) . "\n";
echo "Part 2: " . (distances($orbits, "YOU")["SAN"] - 2) . "\n"; // remove start and target

function distances($orbits, $from) {
    $queue = [$from => 0];

    $distances = [];
    while ($queue) {
        $distance = reset($queue);
        $name = key($queue);
        unset($queue[$name]);

        $distances[$name] = $distance;
        foreach ($orbits[$name] as $l) {
            if (!isset($distances[$l])) {
                $queue[$l] = $distance + 1;
            }
        }
    }

    return $distances;
}
