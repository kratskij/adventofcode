<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

$input = ($test) ? ["2018", "59414"] : ["681901", "681901"];

$recipes = "3710";
$elf1 = 0;
$elf2 = 1;

$recipeLen = strlen($recipes);
$inputLen = strlen($input[1]);
$p1 = $p2 = false;

while (true) {
    $new = ($recipes[$elf1]+$recipes[$elf2]);
    $recipes .= (string)$new;
    if ($new > 9) {
        $recipeLen += 2;
        $equal = substr($recipes, -$inputLen) === $input[1] || substr($recipes, -$inputLen-1, $inputLen) === $input[1];
    } else {
        $recipeLen += 1;
        $equal = substr($recipes, -$inputLen) === $input[1];
    }
    $elf1 = ($elf1 + 1 + $recipes[$elf1]) % $recipeLen;
    $elf2 = ($elf2 + 1 + $recipes[$elf2]) % $recipeLen;
    if ($recipeLen === (int)$input[0] + 10) {
        echo "Part 1: " . substr($recipes, -10) . "\n";
        if ($p2) break;
        $p1 = true;
    }
    if ($equal) {
        $p2 = true;
        echo "Part 2: " . strpos($recipes, $input[1]) . "\n";
        if ($p1) break;
    }
}
