<?php

$test = ($argv[1] ?? false == "test");

$cups = array_map("intval", str_split($test ? 389125467 : 872495136));

echo sprintf(
    "P1: %s\nP2: %s\n",
    implode("", solve($cups, ($test) ? 10 : 100, count($cups) - 1)),
    array_product(solve($cups, 10000000, 2, 1000000))
);

function solve($cups, $moveCount, $returnCount, $totalNumberOfCups = null) {
    $max = max($cups);
    if ($totalNumberOfCups !== null && $totalNumberOfCups > count($cups)) {
        $cups = array_merge($cups, range($max + 1, $totalNumberOfCups));
    }

    $links = [];
    foreach ($cups as $idx => $c) {
        $links[$c] = isset($cups[$idx+1]) ? $cups[$idx+1] : $cups[0];
    }

    $min = min($cups);
    $max = max($cups);

    $currentLabel = reset($cups);
    for ($i = 0; $i < $moveCount; $i++) {
        $currentLabel = move($links, $currentLabel, $min, $max);
    }

    $pos = 1;
    $ret = [];
    for ($i = 0; $i < $returnCount; $i++) {
        $pos = $links[$pos];
        $ret[] = $pos;
    }

    return $ret;
}

function move(&$links, $currentLabel, $min, $max) {
    $firstPick = $links[$currentLabel];

    $destinationLabel = $currentLabel - 1;
    if ($destinationLabel < $min) {
        $destinationLabel = $max;
    }
    while (
        $destinationLabel === $firstPick ||
        $destinationLabel === $links[$firstPick] ||
        $destinationLabel === $links[$links[$firstPick]]
    ) {
        $destinationLabel--;
        if ($destinationLabel < $min) {
            $destinationLabel = $max;
        }
    }

    $links[$currentLabel] = $links[$links[$links[$firstPick]]];
    $links[$links[$links[$firstPick]]] = $links[$destinationLabel];
    $links[$destinationLabel] = $firstPick;

    return $links[$currentLabel];
}
