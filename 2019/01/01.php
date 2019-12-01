<?php

require_once(__DIR__."/../inputReader.php");
$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";
$ir = new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file);
$input = $ir->trim()->lines();

$initFuel = $totalFuel = 0;
foreach ($input as $line) {
    $initFuel += massToFuel($line, false);
    $totalFuel += massToFuel($line, true);
}

echo "Part 1: $initFuel\n";
echo "Part 2: $totalFuel\n";

function massToFuel($mass, $adjust) {
    $fuel = max(0, floor((int)$mass/3) - 2);
    if ($adjust && $fuel > 0) {
        $fuel += massToFuel($fuel, true);
    }
    return  $fuel;
}
