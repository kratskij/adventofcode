<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");
require_once __DIR__ . "/../../Toolbox.php";

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = $ir->lines();

$origMonkeys = [];

while ($input) {
    $monkeyLine = array_shift($input);
    $items = array_map("intval", explode(", ", explode(": ", array_shift($input))[1]));
    $operation = explode(": new = old ", array_shift($input))[1];
    $divisibleBy = (int)explode(": divisible by ", array_shift($input))[1];
    $true = (int)explode(": throw to monkey ", array_shift($input))[1];
    $false = (int)explode(": throw to monkey ", array_shift($input))[1];
    if ($input) {
        $null = array_shift($input);
    }

    $origMonkeys[] = [
        "items" => $items,
        "operation" => $operation[0],
        "operationValue" => substr($operation, 2),
        "divisibleBy" => $divisibleBy,
        "true" => $true,
        "false" => $false,
    ];
}
$lcm = lcm(array_map(function($m){ return $m["divisibleBy"]; }, $origMonkeys));

$monkeyBusinesses = [];
foreach ([1 => 20, 2 => 10000] as $part => $stopAt) {
    $monkeys = $origMonkeys;
    $monkeyInspectCount = array_fill_keys(array_keys($monkeys), 0);
    for ($i = 0; $i < $stopAt; $i++) {
        foreach ($monkeys as $monkeyId => &$monkey) {
            while($item = array_shift($monkey["items"])) {
                $monkeyInspectCount[$monkeyId]++;
                if ($monkey["operationValue"] == "old") {
                    $operationValue = $item;
                } else {
                    $operationValue = $monkey["operationValue"];
                }

                $worryLevel = ($monkey["operation"] == "+") ? $item + $operationValue : $item * $operationValue;

                if ($part == 1) {
                    $worryLevel = floor($worryLevel / 3);
                }
                $worryLevel = $worryLevel % $lcm;
                
                if (($worryLevel % $monkey["divisibleBy"]) == 0) {
                    $monkeys[$monkey["true"]]["items"][] = $worryLevel;
                } else {
                    $monkeys[$monkey["false"]]["items"][] = $worryLevel;
                }
            }
        }
    }
    sort($monkeyInspectCount);
    $monkeyBusinesses[] = array_product(array_slice($monkeyInspectCount, -2));
}

list($p1, $p2) = $monkeyBusinesses;
echo "P1: $p1\nP2: $p2\n";
