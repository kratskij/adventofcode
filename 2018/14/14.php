<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

$inputP1 = ($test) ? "2018" : "681901";
$inputP2 = ($test) ? "59414" : "681901";

$recipes = "3710";
$elf1 = 0;
$elf2 = 1;

$recipeLen = strlen($recipes);
$inputLen = strlen($inputP2);
$p1 = $p2 = false;

while (true) {
    $new = ($recipes[$elf1]+$recipes[$elf2]);
    $recipes .= (string)$new;
    $equal = substr($recipes, -$inputLen) === $inputP2;
    if ($new > 9) {
        $recipeLen += 2;
        $equal = $equal || substr($recipes, -$inputLen-1, $inputLen) === $inputP2;
    } else {
        $recipeLen += 1;
    }
    $elf1 = ($elf1 + 1 + $recipes[$elf1]) % $recipeLen;
    $elf2 = ($elf2 + 1 + $recipes[$elf2]) % $recipeLen;
    if (!$p1 && $recipeLen === (int)$inputP1 + 10) {
        echo "Part 1: " . substr($recipes, -10) . "\n";
        if ($p2) break;
        $p1 = true;
    }
    if ($equal) {
        $p2 = true;
        echo "Part 2: " . strpos($recipes, $inputP2) . "\n";
        if ($p1) break;
    }
}
