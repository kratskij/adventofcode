<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once(__DIR__."/../inputReader.php");

$ir = new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file);
$input = $ir->raw();

list($from, $to) = array_map("intval", explode("-", $input));
$part1 = $part2 = 0;

for ($i = $from; $i <= $to; $i++) {
    $part1 += valid($i);
    $part2 += valid($i, true);
}

echo "Part 1: $part1\n";
echo "Part 2: $part2\n"; #798, 227

function valid($number, $partOfGroup=false) {
    $hasDouble = false;

    $chars = str_split($number);
    foreach ($chars as $key => $c) {
        if (isset($chars[$key+1])) {
            if ($chars[$key+1] < $c) {
                return false;
            }
            $hasDouble = $hasDouble || ($chars[$key+1] == $c && !(
                $partOfGroup && (
                    (isset($chars[$key+2]) && $chars[$key+2] == $c) ||
                    (isset($chars[$key-1]) && $chars[$key-1] == $c)
                )
            ));
        }
    }
    return $hasDouble;
}
