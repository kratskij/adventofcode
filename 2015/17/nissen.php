<?php
$sizes = [33,14,18,20,45,35,16,35,1,13,18,13,50,44,48,6,24,41,30,42];
$combinations = find($sizes, 150);

echo "Part 1: " . array_sum($combinations) . "\n";
echo "Part 2: " . $combinations[min(array_keys($combinations))] . "\n";

function find($sizes, $mustMatch, $matches = [], $depth = 1) {
    $sizes = array_filter($sizes, function($v) use ($mustMatch) { return ($v <= $mustMatch); });
    while ($size = array_shift($sizes)) {
        if ($mustMatch == $size) {
            (isset($matches[$depth])) ? $matches[$depth]++ : $matches[$depth] = 1;
        } else if ($mustMatch < $size) {
            continue;
        } else {
            $matches = find($sizes, $mustMatch - $size, $matches, $depth + 1);
        }
    }
    return $matches;
}
