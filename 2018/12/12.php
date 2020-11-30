<?php

$time = microtime(true);

#ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once(__DIR__."/../inputReader.php");

$ir = new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file);
$input = $ir->lines();

echo "Input: " . (microtime(true) - $time) . "\n"; $time = microtime(true);

$pots = [];
foreach (str_split(array_shift($input)) as $i => $pot) {
    if ($pot == "#") {
        $pots[$i] = true;
    }
}
array_shift($input); // empty line

$patterns = [];
foreach ($input as $k => $line) {
    $p = explode(" ", $line);
    if ($p[2] == "#") {
        $patterns[$p[0]] = true;
    }
}

$prevState = viddify($pots);
$prevPotSum =  array_sum(array_keys($pots));
$stopAt = 50000000000;
for ($gen = 1; $gen <= $stopAt; $gen++) {
    $from = min(array_keys($pots)) - 2;
    $to = max(array_keys($pots)) + 2;
    $prevPots = $pots;
    for ($i = $from; $i <= $to; $i++) {
        $pattern =
            (isset($prevPots[$i-2]) ? "#" : ".") .
            (isset($prevPots[$i-1]) ? "#" : ".") .
            (isset($prevPots[$i]) ? "#" : ".") .
            (isset($prevPots[$i+1]) ? "#" : ".") .
            (isset($prevPots[$i+2]) ? "#" : ".");
        if (isset($patterns[$pattern]) && $patterns[$pattern] == "#") {
            $pots[$i] = true;
        } else {
            unset($pots[$i]);
        }
    }
    $state = viddify($pots);
    $potSum = array_sum(array_keys($pots));
    if ($state == $prevState) { //We've stopped evolving!
        echo "Part2 : " . (microtime(true) - $time) . "\n";
        echo "Part 2: " . ($potSum+(($stopAt-$gen)*($potSum-$prevPotSum))) . "\n";
        $time = microtime(true);
        break;
    }
    if ($gen == 20) {
        echo "Part 1: " . (microtime(true) - $time) . "\n";
        echo "Part 1: $potSum\n";
        $time = microtime(true);
    }
    $prevPotSum =  $potSum;
    $prevState = $state;
}

function viddify($pots) {
    $viddy = "";
    $to = max(array_keys($pots));
    for ($i = min(array_keys($pots)) - 2; $i <= $to + 2; $i++) {
        $viddy .= (isset($pots[$i])) ? "#" : ".";
    }
    return $viddy;
}
