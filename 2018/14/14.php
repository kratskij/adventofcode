<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

$input = ($test) ? ["2018", "59414"] : ["681901", "681901"];

$recipes = "3710";
$elfs = [
    ['index' => 0],
    ['index' => 1]
];

while (strlen($recipes) < $input[0] + 10) {
    $sum = 0;
    foreach ($elfs as $idx => $e) {
        $sum += $recipes[$e["index"]];
    }
    $recipes .= (string)$sum;
    foreach ($elfs as $idx => $e) {
        $elfs[$idx]["index"] = ($e["index"] + 1 + $recipes[$e["index"]]) % strlen($recipes);
    }
}
echo "Part 1: " . substr($recipes, -10) . "\n";

$recipes = "3710";

$elfs = [
    ['index' => 0],
    ['index' => 1]
];

while (strpos($recipes, $input[1]) === false) {
    $sum = 0;
    foreach ($elfs as $idx => $e) {
        $sum += (int)$recipes[$e["index"]];
    }
    $recipes .= (string)$sum;
    foreach ($elfs as $idx => $e) {
        $elfs[$idx]["index"] = ($e["index"] + 1 + $recipes[$e["index"]]) % strlen($recipes);
    }
}
echo "Part 2: " . strpos($recipes, $input[1]) . "\n";
