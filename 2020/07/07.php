<?php

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = $ir->lines();

$bags = $parents = [];

foreach ($input as $line) {
    list($parent, $children) = explode(" contain ", $line);
    $children = array_map("trim", explode(", ", $children));
    $parent = explode(" bag", $parent)[0];
    foreach ($children as $child) {
        $childId = explode(" bag", $child)[0];
        $parts = explode(" ", $childId);
        $count = (int)array_shift($parts);
        $childId = implode(" ", $parts);

        $bags[$parent][$childId] = $count;
        $parents[$childId][$parent] = $parent;
    }
}

$candidates = [];
$q = ["shiny gold"];
while ($q) {
    $el = array_pop($q);
    if (isset($parents[$el])) {
        foreach ($parents[$el] as $p) {
            $candidates[$p] = $p;
            $q[] = $p;
        }
    }
}

$p2 = [];
$sum = -1; // we don't want to count the shiny gold bag itself
$q = [["bag" => "shiny gold", "c" => 1]];

while ($q) {
    $el = array_pop($q);
    $sum += $el["c"];
    if (isset($bags[$el["bag"]])) {
        foreach ($bags[$el["bag"]] as $p => $c) {
            $q[] = ["bag" => $p, "c" => $c * $el["c"]];
        }
    }
}


echo sprintf("P1: %s\nP2: %s\n", count($candidates), $sum);
